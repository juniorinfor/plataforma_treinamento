<?php

namespace App\Livewire\Gamification;

use App\Enums\ChallengeType;
use App\Models\Challenge;
use App\Models\UserChallengeCompletion;
use App\Services\GamificationService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Desafios')]
class ChallengesPage extends Component
{
    // ── Computed ──────────────────────────────────────────────────────

    #[Computed]
    public function challengeGroups(): array
    {
        $user    = auth()->user();
        $service = app(GamificationService::class);

        $challenges = Challenge::where('is_active', true)
            ->where(fn ($q) =>
                $q->whereNull('company_id')->orWhere('company_id', $user->company_id)
            )
            ->where(fn ($q) =>
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now())
            )
            ->where(fn ($q) =>
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now())
            )
            ->orderBy('type')
            ->orderByDesc('xp_reward')
            ->get();

        $groups = [];

        foreach ($challenges as $challenge) {
            $type      = $challenge->type instanceof ChallengeType ? $challenge->type->value : $challenge->type;
            $periodKey = $this->periodKey($challenge);

            $done = UserChallengeCompletion::where('user_id', $user->id)
                ->where('challenge_id', $challenge->id)
                ->where('period_key', $periodKey)
                ->exists();

            [$current, $target] = $service->challengeProgress($user, $challenge);

            if (!isset($groups[$type])) {
                $groups[$type] = ['label' => $this->typeLabel($type), 'items' => []];
            }

            $groups[$type]['items'][] = [
                'challenge' => $challenge,
                'done'      => $done,
                'current'   => $current,
                'target'    => $target,
                'pct'       => $target > 0 ? min(100, round(($current / $target) * 100)) : 0,
            ];
        }

        return $groups;
    }

    #[Computed]
    public function completedToday(): int
    {
        return UserChallengeCompletion::where('user_id', auth()->id())
            ->whereDate('completed_at', today())
            ->count();
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function periodKey(Challenge $challenge): string
    {
        $type = $challenge->type instanceof ChallengeType
            ? $challenge->type->value
            : (string) $challenge->type;

        return match ($type) {
            'weekly'  => now()->format('Y-\WW'),
            'monthly' => now()->format('Y-m'),
            default   => now()->format('Y-m-d'),
        };
    }

    private function typeLabel(string $type): string
    {
        return match ($type) {
            'daily'   => 'Desafios Diários',
            'weekly'  => 'Desafios Semanais',
            'monthly' => 'Desafios Mensais',
            default   => 'Especiais',
        };
    }

    public function render()
    {
        return view('livewire.gamification.challenges-page');
    }
}
