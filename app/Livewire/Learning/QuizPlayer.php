<?php

namespace App\Livewire\Learning;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\PointTransaction;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\UserPoints;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Prova')]
class QuizPlayer extends Component
{
    public int    $lessonId;
    public string $courseSlug  = '';
    public string $courseTitle = '';
    public ?int   $quizId      = null;
    public ?int   $attemptId   = null;
    public bool   $noQuiz      = false;
    public bool   $noAttempts  = false;
    public int    $currentIdx  = 0;
    public bool   $submitted   = false;

    // Respostas em memória: [questionId => ['option_id' => int|null, 'value' => string]]
    public array  $answers    = [];

    // Resultado após envio
    public ?array $resultData = null;

    public function mount(int $id): void
    {
        $lesson = Lesson::with('module.course')->findOrFail($id);
        $this->lessonId    = $id;
        $this->courseSlug  = $lesson->module->course->slug ?? '';
        $this->courseTitle = $lesson->module->course->title ?? '';

        $quiz = Quiz::where('lesson_id', $id)
            ->with(['questions' => fn ($q) => $q->orderBy('sort_order')->with('options')])
            ->first();

        if (!$quiz) {
            $this->noQuiz = true;
            return;
        }

        $this->quizId = $quiz->id;
        $this->initAttempt($quiz);
    }

    // ── Computed ──────────────────────────────────────────────────────

    #[Computed]
    public function quiz(): ?Quiz
    {
        if (!$this->quizId) return null;
        return Quiz::with(['questions' => fn ($q) => $q->orderBy('sort_order')->with('options')])->find($this->quizId);
    }

    #[Computed]
    public function questions(): Collection
    {
        return $this->quiz?->questions ?? collect();
    }

    #[Computed]
    public function currentQuestion(): ?Question
    {
        return $this->questions->get($this->currentIdx);
    }

    #[Computed]
    public function answeredCount(): int
    {
        $count = 0;
        foreach ($this->questions as $q) {
            $a = $this->answers[$q->id] ?? [];
            if ($q->type->value === 'fill_blank' ? !empty($a['value']) : !empty($a['option_id'])) {
                $count++;
            }
        }
        return $count;
    }

    #[Computed]
    public function allAnswered(): bool
    {
        return $this->questions->count() > 0 && $this->answeredCount === $this->questions->count();
    }

    // ── Navegação ─────────────────────────────────────────────────────

    public function prev(): void
    {
        if ($this->currentIdx > 0) $this->currentIdx--;
    }

    public function next(): void
    {
        if ($this->currentIdx < $this->questions->count() - 1) $this->currentIdx++;
    }

    public function goTo(int $index): void
    {
        if ($index >= 0 && $index < $this->questions->count()) {
            $this->currentIdx = $index;
        }
    }

    // ── Respostas ─────────────────────────────────────────────────────

    public function selectOption(int $questionId, int $optionId): void
    {
        if (array_key_exists($questionId, $this->answers)) {
            $this->answers[$questionId]['option_id'] = $optionId;
        }
    }

    // ── Envio ─────────────────────────────────────────────────────────

    public function submit(): void
    {
        if ($this->submitted) return;

        $quiz      = $this->quiz;
        $questions = $this->questions;
        $attempt   = QuizAttempt::find($this->attemptId);
        if (!$attempt) return;

        $totalScore = 0;
        $maxScore   = 0;
        $review     = [];

        foreach ($questions as $question) {
            $answer = $this->answers[$question->id] ?? [];
            $maxScore += $question->points;

            $isCorrect     = false;
            $pointsEarned  = 0;
            $selectedOptId = null;
            $textAnswer    = null;
            $givenLabel    = '—';
            $correctLabel  = '—';

            if ($question->type->value === 'fill_blank') {
                $textAnswer   = trim($answer['value'] ?? '');
                $correctOpt   = $question->options->firstWhere('is_correct', true);
                $correctLabel = $correctOpt?->content ?? '';
                $givenLabel   = $textAnswer !== '' ? $textAnswer : '—';
                $isCorrect    = $textAnswer !== '' && strcasecmp($textAnswer, trim($correctLabel)) === 0;
            } else {
                $selectedOptId = $answer['option_id'] ?? null;
                $selectedOpt   = $selectedOptId ? $question->options->firstWhere('id', $selectedOptId) : null;
                $correctOpt    = $question->options->firstWhere('is_correct', true);
                $isCorrect     = (bool) ($selectedOpt?->is_correct ?? false);
                $givenLabel    = $selectedOpt?->content ?? '—';
                $correctLabel  = $correctOpt?->content ?? '—';
            }

            if ($isCorrect) {
                $pointsEarned = $question->points;
                $totalScore  += $pointsEarned;
            }

            QuizAttemptAnswer::create([
                'quiz_attempt_id'    => $attempt->id,
                'question_id'        => $question->id,
                'selected_option_id' => $selectedOptId,
                'text_answer'        => $textAnswer,
                'is_correct'         => $isCorrect,
                'points_earned'      => $pointsEarned,
                'created_at'         => now(),
            ]);

            $review[] = [
                'content'     => $question->content,
                'type'        => $question->type->value,
                'is_correct'  => $isCorrect,
                'given'       => $givenLabel,
                'correct'     => $correctLabel,
                'explanation' => $question->explanation,
                'points'      => $question->points,
                'earned'      => $pointsEarned,
            ];
        }

        $percentage = $maxScore > 0 ? round($totalScore / $maxScore * 100, 2) : 0;
        $passed     = $percentage >= $quiz->passing_score;

        $attempt->update([
            'score'              => $totalScore,
            'max_score'          => $maxScore,
            'percentage'         => $percentage,
            'passed'             => $passed,
            'completed_at'       => now(),
            'time_spent_seconds' => now()->diffInSeconds($attempt->started_at),
        ]);

        $xpEarned = 0;
        if ($passed && $quiz->xp_reward > 0) {
            $user       = auth()->user();
            $userPoints = UserPoints::where('user_id', $user->id)
                ->where('company_id', $user->company_id)
                ->first();

            if ($userPoints) {
                $xpEarned = $quiz->xp_reward;
                $userPoints->increment('total_xp', $xpEarned);
                $userPoints->increment('weekly_xp', $xpEarned);
                $userPoints->increment('monthly_xp', $xpEarned);

                PointTransaction::create([
                    'user_id'        => $user->id,
                    'company_id'     => $user->company_id,
                    'xp_amount'      => $xpEarned,
                    'type'           => 'quiz_pass',
                    'description'    => "Aprovado: {$quiz->title}",
                    'reference_type' => QuizAttempt::class,
                    'reference_id'   => $attempt->id,
                    'created_at'     => now(),
                ]);
            }

            LessonProgress::updateOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $this->lessonId],
                ['status' => 'completed', 'completed_at' => now()]
            );
        }

        $this->resultData = [
            'score'      => $totalScore,
            'max_score'  => $maxScore,
            'percentage' => $percentage,
            'passed'     => $passed,
            'xp_earned'  => $xpEarned,
            'review'     => $review,
        ];

        $this->submitted = true;
        unset($this->quiz, $this->questions, $this->currentQuestion, $this->answeredCount, $this->allAnswered);
    }

    public function retry(): void
    {
        $this->submitted  = false;
        $this->resultData = null;
        $this->currentIdx = 0;
        $this->answers    = [];
        $this->attemptId  = null;
        $this->noAttempts = false;

        $quiz = Quiz::where('lesson_id', $this->lessonId)
            ->with(['questions' => fn ($q) => $q->orderBy('sort_order')->with('options')])
            ->first();

        if ($quiz) {
            $this->initAttempt($quiz);
        }

        unset($this->quiz, $this->questions, $this->currentQuestion, $this->answeredCount, $this->allAnswered);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function initAttempt(Quiz $quiz): void
    {
        $completedCount = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->count();

        if ($quiz->max_attempts && $completedCount >= $quiz->max_attempts) {
            $this->noAttempts = true;
            return;
        }

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', auth()->id())
            ->whereNull('completed_at')
            ->latest()
            ->first();

        if (!$attempt) {
            $attempt = QuizAttempt::create([
                'quiz_id'          => $quiz->id,
                'user_id'          => auth()->id(),
                'hearts_remaining' => $quiz->hearts_enabled ? $quiz->hearts_count : 99,
                'max_score'        => $quiz->questions->sum('points'),
                'started_at'       => now(),
            ]);
        }

        $this->attemptId = $attempt->id;

        foreach ($quiz->questions as $question) {
            $this->answers[$question->id] = ['option_id' => null, 'value' => ''];
        }
    }

    public function render()
    {
        return view('livewire.learning.quiz-player');
    }
}
