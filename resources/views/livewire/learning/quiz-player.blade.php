<div class="max-w-2xl mx-auto animate-fade-in">
    @if(!$quizComplete)
    {{-- Quiz Header --}}
    <div class="flex items-center justify-between mb-6">
        <a href="#" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </a>
        {{-- Progress --}}
        <div class="flex-1 mx-6">
            <div class="w-full bg-gray-100 rounded-full h-3">
                <div class="h-3 rounded-full transition-all duration-500" style="width: {{ $quiz->questions->count() > 0 ? (($currentQuestion + 1) / $quiz->questions->count()) * 100 : 0 }}%; background: var(--tu-primary)"></div>
            </div>
        </div>
        {{-- Hearts --}}
        <div class="flex items-center gap-1">
            @for($i = 0; $i < 5; $i++)
            <svg class="w-6 h-6 {{ $i < $hearts ? 'tu-heart' : 'tu-heart-empty' }} {{ $i === $hearts && $answered && !$isCorrect ? 'animate-heart-break' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
            </svg>
            @endfor
        </div>
    </div>

    {{-- Question --}}
    @if($quiz->questions->count() > 0)
    @php $question = $quiz->questions[$currentQuestion] ?? $quiz->questions->first(); @endphp
    <div class="tu-card p-6 sm:p-8 mb-6">
        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Questao {{ $currentQuestion + 1 }} de {{ $quiz->questions->count() }}</span>
        <h2 class="text-xl font-bold text-gray-900 mt-3 mb-6">{{ $question->content }}</h2>

        <div class="space-y-3">
            @foreach($question->options as $option)
            <button class="tu-quiz-option w-full text-left flex items-center gap-3 {{ $selectedOption === $option->id ? ($answered ? ($option->is_correct ? 'correct' : 'wrong') : 'selected') : '' }} {{ $answered && $option->is_correct ? 'correct' : '' }}">
                <span class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-sm font-bold shrink-0 {{ $selectedOption === $option->id ? 'border-current bg-current/10' : 'border-gray-300' }}">
                    {{ chr(65 + $loop->index) }}
                </span>
                <span>{{ $option->content }}</span>
                @if($answered && $option->is_correct)
                <svg class="w-5 h-5 ml-auto text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                @endif
                @if($answered && $selectedOption === $option->id && !$option->is_correct)
                <svg class="w-5 h-5 ml-auto text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                @endif
            </button>
            @endforeach
        </div>

        @if($answered && $question->explanation)
        <div class="mt-6 p-4 rounded-xl {{ $isCorrect ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }} animate-slide-up">
            <p class="text-sm font-semibold {{ $isCorrect ? 'text-green-700' : 'text-red-700' }} mb-1">{{ $isCorrect ? 'Correto!' : 'Incorreto!' }}</p>
            <p class="text-sm {{ $isCorrect ? 'text-green-600' : 'text-red-600' }}">{{ $question->explanation }}</p>
        </div>
        @endif
    </div>

    <button class="tu-btn tu-btn-primary tu-btn-lg w-full">
        {{ $answered ? 'Proxima Questao' : 'Verificar Resposta' }}
    </button>
    @endif

    @else
    {{-- Quiz Complete --}}
    <div class="tu-card p-8 text-center animate-bounce-in">
        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Quiz Concluido!</h2>
        <div class="flex items-center justify-center gap-6 my-6">
            <div>
                <p class="text-3xl font-bold" style="color: var(--tu-success)">{{ $score }}/{{ $quiz->questions->count() }}</p>
                <p class="text-sm text-gray-500">Acertos</p>
            </div>
            <div class="w-px h-12 bg-gray-200"></div>
            <div>
                <p class="text-3xl font-bold" style="color: var(--tu-xp)">+{{ $quiz->xp_reward }}</p>
                <p class="text-sm text-gray-500">XP ganhos</p>
            </div>
            <div class="w-px h-12 bg-gray-200"></div>
            <div>
                <div class="flex gap-1">
                    @for($i = 0; $i < $hearts; $i++)
                    <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                    @endfor
                </div>
                <p class="text-sm text-gray-500 mt-1">Coracoes</p>
            </div>
        </div>
        <div class="flex gap-3 justify-center">
            <button class="tu-btn tu-btn-outline">Tentar Novamente</button>
            <button class="tu-btn tu-btn-primary">Proxima Aula</button>
        </div>
    </div>
    @endif
</div>