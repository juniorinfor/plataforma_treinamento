<?php

namespace App\Livewire\Learning;

use App\Models\Quiz;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class QuizPlayer extends Component
{
    public int $id;
    public int $currentQuestion = 0;
    public int $hearts = 5;
    public int $score = 0;
    public ?int $selectedOption = null;
    public bool $answered = false;
    public bool $isCorrect = false;
    public bool $quizComplete = false;

    public function mount(int $id): void
    {
        $this->id = $id;
    }

    public function render()
    {
        $quiz = Quiz::with('questions.options')->findOrFail($this->id);
        return view('livewire.learning.quiz-player', ['quiz' => $quiz]);
    }
}
