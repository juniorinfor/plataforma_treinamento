<div class="max-w-3xl mx-auto animate-fade-in">
    {{-- Top bar --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('courses.show', $lesson->module->course->slug) }}" class="flex items-center gap-2 text-gray-500 hover:text-gray-700" wire:navigate>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span class="text-sm font-medium">{{ $lesson->module->course->title }}</span>
        </a>
        <span class="text-xs font-bold px-3 py-1 rounded-full" style="background: var(--tu-xp-glow); color: var(--tu-xp-dark)">+{{ $lesson->xp_reward }} XP</span>
    </div>

    {{-- Progress bar --}}
    <div class="w-full bg-gray-100 rounded-full h-2 mb-8">
        <div class="h-2 rounded-full" style="width: 30%; background: var(--tu-primary)"></div>
    </div>

    {{-- Content --}}
    <div class="tu-card p-6 sm:p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $lesson->title }}</h1>

        @foreach($lesson->contents as $content)
            @if($content->type === 'heading')
                <h2 class="text-xl font-bold text-gray-900 mt-8 mb-4">{{ $content->content }}</h2>
            @elseif($content->type === 'text')
                <div class="prose prose-gray max-w-none mb-6 text-gray-700 leading-relaxed">
                    {!! $content->content !!}
                </div>
            @elseif($content->type === 'image')
                <div class="my-6 rounded-xl overflow-hidden bg-gray-100">
                    <img src="{{ $content->content }}" alt="" class="w-full">
                </div>
            @elseif($content->type === 'video')
                <div class="my-6 aspect-video rounded-xl overflow-hidden bg-gray-900">
                    <iframe src="{{ $content->content }}" class="w-full h-full" allowfullscreen></iframe>
                </div>
            @endif
        @endforeach

        @if($lesson->contents->isEmpty())
        <div class="prose prose-gray max-w-none text-gray-700 leading-relaxed">
            <p>Este conteudo sera preenchido em breve. Por enquanto, este e um preview da interface de visualizacao de aulas do TrainUp.</p>
            <p>O sistema suporta varios tipos de conteudo:</p>
            <ul>
                <li><strong>Texto formatado</strong> com titulos, listas e destaques</li>
                <li><strong>Videos</strong> incorporados do YouTube ou Vimeo</li>
                <li><strong>Imagens</strong> ilustrativas e diagramas</li>
                <li><strong>PDFs</strong> para manuais e documentos</li>
                <li><strong>Quizzes interativos</strong> com sistema de coracoes</li>
            </ul>
        </div>
        @endif
    </div>

    {{-- Bottom Action --}}
    <div class="flex items-center justify-between mt-6">
        <button class="tu-btn tu-btn-outline">Anterior</button>
        <button class="tu-btn tu-btn-success tu-btn-lg">
            Marcar como Concluida
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </button>
    </div>
</div>