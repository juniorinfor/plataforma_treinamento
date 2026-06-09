<?php

namespace App\Services;

use App\Enums\DiagnosticAssessmentStatus;
use App\Models\Badge;
use App\Models\Challenge;
use App\Models\DiagnosticAssessment;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Level;
use App\Models\PointTransaction;
use App\Models\Streak;
use App\Models\User;
use App\Models\UserChallengeCompletion;
use App\Models\UserPoints;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GamificationService
{
    // ── Ponto de entrada por evento ───────────────────────────────────

    /**
     * Chamado quando o usuário completa uma aula.
     * Retorna array com tudo que aconteceu: ['xp', 'leveled_up', 'badges', 'challenges']
     */
    public function onLessonComplete(User $user, Lesson $lesson): array
    {
        $xp = 10 + (int) ($lesson->module?->course?->xp_reward / max(1, $lesson->module?->course?->modules?->sum(fn($m) => $m->lessons_count ?? 1)));

        $this->updateStreak($user);

        $leveled = $this->awardXp($user, max(10, $xp), 'lesson', "Aula concluída: {$lesson->title}", $lesson);

        $badges     = $this->checkAndAwardBadges($user);
        $challenges = $this->evaluateChallenges($user);

        return [
            'xp'         => max(10, $xp),
            'leveled_up' => $leveled,
            'badges'     => $badges,
            'challenges' => $challenges,
        ];
    }

    /**
     * Chamado ao concluir um diagnóstico.
     */
    public function onDiagnosticComplete(User $user, DiagnosticAssessment $assessment): array
    {
        $xp = $assessment->tool?->xp_reward ?? 50;

        $this->updateStreak($user);

        $leveled = $this->awardXp($user, $xp, 'diagnostic', "Diagnóstico concluído: {$assessment->tool?->name}", $assessment);

        $badges     = $this->checkAndAwardBadges($user);
        $challenges = $this->evaluateChallenges($user);

        return [
            'xp'         => $xp,
            'leveled_up' => $leveled,
            'badges'     => $badges,
            'challenges' => $challenges,
        ];
    }

    // ── XP ────────────────────────────────────────────────────────────

    /**
     * Concede XP, atualiza UserPoints e verifica subida de nível.
     * Retorna TRUE se o usuário subiu de nível.
     */
    public function awardXp(
        User $user,
        int $amount,
        string $type,
        string $description,
        ?\Illuminate\Database\Eloquent\Model $reference = null
    ): bool {
        if ($amount <= 0) {
            return false;
        }

        // Registra transação
        $txData = [
            'user_id'     => $user->id,
            'company_id'  => $user->company_id,
            'xp_amount'   => $amount,
            'type'        => $type,
            'description' => $description,
        ];

        if ($reference) {
            $txData['reference_type'] = get_class($reference);
            $txData['reference_id']   = $reference->getKey();
        }

        PointTransaction::create($txData);

        // Atualiza contadores
        $points = UserPoints::firstOrCreate(
            ['user_id' => $user->id],
            ['company_id' => $user->company_id, 'total_xp' => 0, 'weekly_xp' => 0, 'monthly_xp' => 0]
        );

        $prevLevel = $points->current_level_id;

        $points->increment('total_xp', $amount);
        $points->increment('weekly_xp', $amount);
        $points->increment('monthly_xp', $amount);

        // Verifica novo nível
        $points->refresh();
        $newLevel = Level::where('min_xp', '<=', $points->total_xp)
            ->orderByDesc('min_xp')
            ->first();

        if ($newLevel && $newLevel->id !== $prevLevel) {
            $points->update(['current_level_id' => $newLevel->id]);
            return true; // subiu de nível
        }

        return false;
    }

    // ── Streak ────────────────────────────────────────────────────────

    public function updateStreak(User $user): Streak
    {
        $streak = Streak::firstOrCreate(
            ['user_id' => $user->id],
            [
                'company_id'              => $user->company_id,
                'current_streak'          => 0,
                'longest_streak'          => 0,
                'streak_freezes_remaining'=> 0,
            ]
        );

        $today     = Carbon::today();
        $lastDate  = $streak->last_activity_date;

        if ($lastDate === null) {
            // Primeiro acesso
            $streak->update([
                'current_streak'  => 1,
                'longest_streak'  => 1,
                'last_activity_date' => $today,
            ]);
        } elseif ($lastDate->isSameDay($today)) {
            // Já registrou hoje — nada a fazer
        } elseif ($lastDate->isSameDay($today->copy()->subDay())) {
            // Ontem → incrementa
            $newStreak = $streak->current_streak + 1;
            $streak->update([
                'current_streak'     => $newStreak,
                'longest_streak'     => max($streak->longest_streak, $newStreak),
                'last_activity_date' => $today,
            ]);
        } else {
            // Quebrou o streak
            $streak->update([
                'current_streak'     => 1,
                'last_activity_date' => $today,
            ]);
        }

        return $streak->fresh();
    }

    // ── Badges ────────────────────────────────────────────────────────

    /**
     * Verifica critérios e concede badges ainda não ganhos.
     * Retorna coleção de badges recém concedidos.
     */
    public function checkAndAwardBadges(User $user): Collection
    {
        $earnedIds = $user->badges()->pluck('badges.id')->toArray();

        $candidates = Badge::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('company_id')->orWhere('company_id', $user->company_id))
            ->whereNotIn('id', $earnedIds)
            ->get();

        $newBadges = collect();

        foreach ($candidates as $badge) {
            if ($this->evaluateBadgeCriteria($user, $badge)) {
                $user->badges()->attach($badge->id, ['earned_at' => now()]);
                // Concede XP do badge
                $this->awardXp($user, $badge->xp_reward, 'badge', "Badge conquistado: {$badge->name}", $badge);
                $newBadges->push($badge);
            }
        }

        return $newBadges;
    }

    private function evaluateBadgeCriteria(User $user, Badge $badge): bool
    {
        $type   = $badge->criteria_type;
        $config = $badge->criteria_config ?? [];
        $target = (int) ($config['count'] ?? 1);

        return match ($type) {
            'lessons_total' => LessonProgress::where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->count() >= $target,

            'streak_days' => Streak::where('user_id', $user->id)
                ->value('current_streak') >= $target,

            'diagnostic_complete' => DiagnosticAssessment::where('user_id', $user->id)
                ->where('status', DiagnosticAssessmentStatus::Completed)
                ->count() >= $target,

            'xp_total' => UserPoints::where('user_id', $user->id)
                ->value('total_xp') >= $target,

            'course_complete' => Enrollment::where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->count() >= $target,

            default => false,
        };
    }

    // ── Challenges ────────────────────────────────────────────────────

    /**
     * Avalia desafios ativos e conclui os que foram atingidos.
     * Retorna desafios recém completados.
     */
    public function evaluateChallenges(User $user): Collection
    {
        $now = now();

        $challenges = Challenge::where('is_active', true)
            ->where(fn ($q) =>
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now)
            )
            ->where(fn ($q) =>
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now)
            )
            ->where(fn ($q) =>
                $q->whereNull('company_id')->orWhere('company_id', $user->company_id)
            )
            ->get();

        $newlyCompleted = collect();

        foreach ($challenges as $challenge) {
            $periodKey = $this->periodKey($challenge);
            $alreadyDone = UserChallengeCompletion::where('user_id', $user->id)
                ->where('challenge_id', $challenge->id)
                ->where('period_key', $periodKey)
                ->exists();

            if ($alreadyDone) {
                continue;
            }

            [$current, $target] = $this->challengeProgress($user, $challenge);

            if ($current >= $target) {
                UserChallengeCompletion::create([
                    'user_id'      => $user->id,
                    'challenge_id' => $challenge->id,
                    'period_key'   => $periodKey,
                    'completed_at' => $now,
                ]);

                $this->awardXp($user, $challenge->xp_reward, 'challenge', "Desafio concluído: {$challenge->title}", $challenge);
                $newlyCompleted->push($challenge);
            }
        }

        return $newlyCompleted;
    }

    /**
     * Calcula o progresso atual de um desafio para o usuário.
     * Retorna [current, target].
     */
    public function challengeProgress(User $user, Challenge $challenge): array
    {
        $config = $challenge->criteria_config ?? [];
        $target = (int) ($config['count'] ?? 1);
        $type   = $challenge->criteria_type;
        $today  = Carbon::today();

        $current = match ($type) {
            'lesson_today' =>
                LessonProgress::where('user_id', $user->id)
                    ->whereNotNull('completed_at')
                    ->whereDate('completed_at', $today)
                    ->count(),

            'lesson_week' =>
                LessonProgress::where('user_id', $user->id)
                    ->whereNotNull('completed_at')
                    ->whereBetween('completed_at', [
                        $today->copy()->startOfWeek(),
                        $today->copy()->endOfWeek(),
                    ])
                    ->count(),

            'streak_min' =>
                min(
                    Streak::where('user_id', $user->id)->value('current_streak') ?? 0,
                    $target
                ),

            'diagnostic_complete' =>
                DiagnosticAssessment::where('user_id', $user->id)
                    ->where('status', DiagnosticAssessmentStatus::Completed)
                    ->count(),

            'xp_today' => (int) PointTransaction::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->sum('xp_amount'),

            default => 0,
        };

        return [(int) $current, $target];
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function periodKey(Challenge $challenge): string
    {
        return match ($challenge->type->value ?? 'daily') {
            'weekly'  => now()->format('Y-\WW'),
            'monthly' => now()->format('Y-m'),
            default   => now()->format('Y-m-d'),
        };
    }
}
