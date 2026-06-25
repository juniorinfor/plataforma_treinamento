<?php

namespace App\Livewire\Admin;

use App\Enums\QuestionType;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Construtor de prova')]
class QuizBuilder extends Component
{
    public int $courseId;
    public int $lessonId;
    public ?int $quizId = null;

    // Configurações da prova
    public string $quizTitle        = '';
    public string $quizDescription  = '';
    public int    $passingScore      = 70;
    public ?int   $timeLimitMinutes  = null;
    public ?int   $maxAttempts       = null;
    public bool   $shuffleQuestions  = false;
    public bool   $heartsEnabled     = false;
    public int    $heartsCount       = 3;
    public int    $xpReward          = 25;

    // Modal de questão
    public bool    $showQuestionModal = false;
    public string  $questionStep      = 'type'; // 'type' | 'form'
    public ?int    $questionId        = null;
    public string  $questionType      = '';
    public string  $questionContent   = '';
    public string  $questionExplain   = '';
    public int     $questionPoints    = 1;
    public array   $questionOptions   = [];   // [{content, is_correct}, ...]
    public string  $tfCorrect         = 'true';
    public string  $fillAnswer        = '';

    public function mount(Course $course, Lesson $lesson): void
    {
        abort_unless(auth()->user()->isPlatformAdmin(), 403);
        abort_unless($lesson->module->course_id === $course->id, 404);

        $this->courseId = $course->id;
        $this->lessonId = $lesson->id;

        $quiz = Quiz::where('lesson_id', $lesson->id)->first();
        if ($quiz) {
            $this->quizId           = $quiz->id;
            $this->quizTitle        = $quiz->title;
            $this->quizDescription  = $quiz->description ?? '';
            $this->passingScore     = $quiz->passing_score;
            $this->timeLimitMinutes = $quiz->time_limit_minutes;
            $this->maxAttempts      = $quiz->max_attempts;
            $this->shuffleQuestions = $quiz->shuffle_questions;
            $this->heartsEnabled    = $quiz->hearts_enabled;
            $this->heartsCount      = $quiz->hearts_count;
            $this->xpReward         = $quiz->xp_reward;
        } else {
            $this->quizTitle = $lesson->title;
        }
    }

    #[Computed]
    public function lesson(): Lesson
    {
        return Lesson::with('module.course')->findOrFail($this->lessonId);
    }

    #[Computed]
    public function questions(): Collection
    {
        if (!$this->quizId) return collect();
        return Question::where('quiz_id', $this->quizId)
            ->with('options')
            ->orderBy('sort_order')
            ->get();
    }

    public function questionTypeList(): array
    {
        return array_map(
            fn (QuestionType $t) => ['value' => $t->value, 'label' => $t->label()],
            QuestionType::cases()
        );
    }

    // ── Configurações ─────────────────────────────────────────────────

    public function saveSettings(): void
    {
        $this->validate([
            'quizTitle'        => ['required', 'string', 'min:2', 'max:200'],
            'quizDescription'  => ['nullable', 'string'],
            'passingScore'     => ['required', 'integer', 'min:1', 'max:100'],
            'timeLimitMinutes' => ['nullable', 'integer', 'min:1', 'max:300'],
            'maxAttempts'      => ['nullable', 'integer', 'min:1', 'max:99'],
            'shuffleQuestions' => ['boolean'],
            'heartsEnabled'    => ['boolean'],
            'heartsCount'      => ['required', 'integer', 'min:1', 'max:10'],
            'xpReward'         => ['required', 'integer', 'min:0', 'max:99999'],
        ]);

        $payload = [
            'lesson_id'          => $this->lessonId,
            'title'              => $this->quizTitle,
            'description'        => $this->quizDescription ?: null,
            'passing_score'      => $this->passingScore,
            'time_limit_minutes' => $this->timeLimitMinutes,
            'max_attempts'       => $this->maxAttempts,
            'shuffle_questions'  => $this->shuffleQuestions,
            'hearts_enabled'     => $this->heartsEnabled,
            'hearts_count'       => $this->heartsCount,
            'xp_reward'          => $this->xpReward,
        ];

        if ($this->quizId) {
            Quiz::where('id', $this->quizId)->update($payload);
        } else {
            $quiz = Quiz::create($payload);
            $this->quizId = $quiz->id;
        }

        $this->dispatch('settings-saved');
    }

    // ── Modal de questão ──────────────────────────────────────────────

    public function openNewQuestion(): void
    {
        if (!$this->quizId) {
            $quiz = Quiz::create([
                'lesson_id'      => $this->lessonId,
                'title'          => $this->quizTitle ?: $this->lesson->title,
                'passing_score'  => $this->passingScore,
                'hearts_enabled' => $this->heartsEnabled,
                'hearts_count'   => $this->heartsCount,
                'xp_reward'      => $this->xpReward,
            ]);
            $this->quizId = $quiz->id;
        }
        $this->resetQuestionForm();
        $this->questionStep = 'type';
        $this->showQuestionModal = true;
    }

    public function selectType(string $type): void
    {
        $this->questionType = $type;
        $this->initDefaultOptions($type);
        $this->questionStep = 'form';
    }

    public function openEditQuestion(int $id): void
    {
        $question = Question::with('options')->find($id);
        if (!$question) return;

        $this->resetValidation();
        $this->questionId      = $id;
        $this->questionType    = $question->type->value;
        $this->questionContent = $question->content;
        $this->questionExplain = $question->explanation ?? '';
        $this->questionPoints  = $question->points;
        $this->questionStep    = 'form';

        match ($question->type) {
            QuestionType::TrueFalse => $this->tfCorrect = $question->options->firstWhere('is_correct', true)?->content === 'Verdadeiro' ? 'true' : 'false',
            QuestionType::FillBlank => $this->fillAnswer = $question->options->firstWhere('is_correct', true)?->content ?? '',
            default => $this->questionOptions = $question->options->map(fn ($o) => [
                'content'    => $o->content,
                'is_correct' => $o->is_correct,
            ])->toArray(),
        };

        $this->showQuestionModal = true;
    }

    public function addOption(): void
    {
        if (count($this->questionOptions) < 4) {
            $this->questionOptions[] = ['content' => '', 'is_correct' => false];
        }
    }

    public function removeOption(int $index): void
    {
        if (count($this->questionOptions) > 2) {
            array_splice($this->questionOptions, $index, 1);
            $this->questionOptions = array_values($this->questionOptions);
        }
    }

    public function saveQuestion(): void
    {
        $rules = [
            'questionContent' => ['required', 'string', 'min:3'],
            'questionPoints'  => ['required', 'integer', 'min:1', 'max:100'],
        ];

        if ($this->questionType === QuestionType::MultipleChoice->value) {
            $rules['questionOptions']              = ['required', 'array', 'min:2', 'max:4'];
            $rules['questionOptions.*.content']    = ['required', 'string', 'min:1'];
            $rules['questionOptions.*.is_correct'] = ['boolean'];
        } elseif ($this->questionType === QuestionType::FillBlank->value) {
            $rules['fillAnswer'] = ['required', 'string', 'min:1', 'max:255'];
        }

        $this->validate($rules);

        if ($this->questionType === QuestionType::MultipleChoice->value) {
            if (!collect($this->questionOptions)->contains('is_correct', true)) {
                $this->addError('questionOptions', 'Marque pelo menos uma alternativa como correta.');
                return;
            }
        }

        $baseOrder = $this->questionId
            ? Question::find($this->questionId)->sort_order
            : (int) Question::where('quiz_id', $this->quizId)->max('sort_order') + 1;

        $data = [
            'quiz_id'     => $this->quizId,
            'type'        => $this->questionType,
            'content'     => $this->questionContent,
            'explanation' => $this->questionExplain ?: null,
            'points'      => $this->questionPoints,
            'sort_order'  => $baseOrder,
        ];

        if ($this->questionId) {
            $question = Question::find($this->questionId);
            $question->update($data);
            $question->options()->delete();
        } else {
            $question = Question::create($data);
        }

        $this->persistOptions($question);

        unset($this->questions);
        $this->showQuestionModal = false;
    }

    public function deleteQuestion(int $id): void
    {
        Question::where('id', $id)->where('quiz_id', $this->quizId)->delete();
        unset($this->questions);
    }

    public function moveQuestion(int $id, string $dir): void
    {
        $items = $this->questions->all();
        $idx   = null;
        foreach ($items as $i => $item) {
            if ($item->id === $id) { $idx = $i; break; }
        }
        if ($idx === null) return;

        $swap = $dir === 'up' ? $idx - 1 : $idx + 1;
        if ($swap < 0 || $swap >= count($items)) return;

        $a = $items[$idx];
        $b = $items[$swap];
        $tmp = $a->sort_order;
        $a->update(['sort_order' => $b->sort_order]);
        $b->update(['sort_order' => $tmp]);
        unset($this->questions);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function resetQuestionForm(): void
    {
        $this->resetValidation();
        $this->questionId      = null;
        $this->questionType    = '';
        $this->questionContent = '';
        $this->questionExplain = '';
        $this->questionPoints  = 1;
        $this->questionOptions = [];
        $this->tfCorrect       = 'true';
        $this->fillAnswer      = '';
    }

    private function initDefaultOptions(string $type): void
    {
        if ($type === QuestionType::MultipleChoice->value) {
            $this->questionOptions = [
                ['content' => '', 'is_correct' => false],
                ['content' => '', 'is_correct' => false],
            ];
        } else {
            $this->questionOptions = [];
            $this->tfCorrect = 'true';
            $this->fillAnswer = '';
        }
    }

    private function persistOptions(Question $question): void
    {
        if ($this->questionType === QuestionType::MultipleChoice->value) {
            foreach ($this->questionOptions as $i => $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'content'     => $opt['content'],
                    'is_correct'  => (bool) $opt['is_correct'],
                    'sort_order'  => $i,
                ]);
            }
        } elseif ($this->questionType === QuestionType::TrueFalse->value) {
            QuestionOption::create(['question_id' => $question->id, 'content' => 'Verdadeiro', 'is_correct' => $this->tfCorrect === 'true',  'sort_order' => 0]);
            QuestionOption::create(['question_id' => $question->id, 'content' => 'Falso',      'is_correct' => $this->tfCorrect === 'false', 'sort_order' => 1]);
        } elseif ($this->questionType === QuestionType::FillBlank->value) {
            QuestionOption::create([
                'question_id' => $question->id,
                'content'     => $this->fillAnswer,
                'is_correct'  => true,
                'sort_order'  => 0,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.quiz-builder');
    }
}
