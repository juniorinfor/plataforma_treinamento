@php
    $interactive = $interactive ?? false;
    $calloutStyles = $calloutStyles ?? [
        'info'    => ['label' => 'Informação', 'bg' => 'bg-blue-50',    'border' => 'border-blue-200',    'text' => 'text-blue-800',    'icon' => 'ℹ️'],
        'tip'     => ['label' => 'Dica',        'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-800', 'icon' => '💡'],
        'warning' => ['label' => 'Atenção',     'bg' => 'bg-amber-50',   'border' => 'border-amber-200',   'text' => 'text-amber-800',   'icon' => '⚠️'],
        'success' => ['label' => 'Sucesso',     'bg' => 'bg-green-50',   'border' => 'border-green-200',   'text' => 'text-green-800',   'icon' => '✅'],
    ];
@endphp

@if($block->type === 'text')
<div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-wrap text-sm leading-relaxed">{{ $block->content }}</div>

@elseif($block->type === 'rich')
<div class="text-gray-700 text-sm leading-relaxed
            [&_h1]:text-xl [&_h1]:font-bold [&_h1]:text-gray-900 [&_h1]:mt-2 [&_h1]:mb-2
            [&_h2]:text-lg [&_h2]:font-bold [&_h2]:text-gray-900 [&_h2]:mt-3 [&_h2]:mb-2
            [&_h3]:text-base [&_h3]:font-bold [&_h3]:text-gray-900 [&_h3]:mt-3 [&_h3]:mb-1
            [&_p]:mb-3 [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:mb-3 [&_ul]:space-y-1
            [&_ol]:list-decimal [&_ol]:pl-5 [&_ol]:mb-3 [&_ol]:space-y-1
            [&_strong]:font-bold [&_strong]:text-gray-900 [&_em]:italic
            [&_a]:text-indigo-600 [&_a]:underline [&_a]:font-medium
            [&_blockquote]:border-l-4 [&_blockquote]:border-gray-200 [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:text-gray-500
            [&_code]:bg-gray-100 [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:rounded [&_code]:text-xs">
    {!! \Illuminate\Support\Str::markdown($block->content ?? '') !!}
</div>

@elseif($block->type === 'video')
@php $embedUrl = $block->settings['embed_url'] ?? null; @endphp
@if($embedUrl)
<div class="rounded-xl overflow-hidden bg-black aspect-video">
    <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen></iframe>
</div>
@else
<p class="text-sm text-gray-400">URL inválida: {{ $block->content }}</p>
@endif

@elseif($block->type === 'video_placeholder')
<div class="rounded-xl border-2 border-dashed border-purple-200 bg-purple-50/50 p-6 text-center">
    <p class="text-3xl mb-2">🎬</p>
    <p class="text-sm font-bold text-purple-700">Vídeo-aula em breve</p>
    @if($block->content)
    <p class="text-xs text-purple-500 mt-1">{{ $block->content }}</p>
    @endif
</div>

@elseif($block->type === 'pdf')
@php
    $filename = $block->settings['filename'] ?? basename($block->content);
    $size     = $block->settings['size'] ?? null;
    $kb       = $size ? round($size / 1024) : null;
@endphp
<div class="flex items-center gap-4 p-3 rounded-xl bg-red-50 border border-red-100">
    <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
    </div>
    <div class="min-w-0 flex-1">
        <p class="text-sm font-semibold text-gray-800 truncate">{{ $filename }}</p>
        @if($kb) <p class="text-xs text-gray-400">{{ $kb }} KB</p> @endif
    </div>
    <a href="{{ asset('storage/' . $block->content) }}" target="_blank" download
       class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-semibold">
        Baixar
    </a>
</div>

@elseif($block->type === 'image')
@php $caption = $block->settings['caption'] ?? null; @endphp
<figure>
    <img src="{{ asset('storage/' . $block->content) }}" alt="{{ $caption }}" class="w-full rounded-xl border border-gray-100">
    @if($caption)
    <figcaption class="text-xs text-gray-400 mt-2 text-center">{{ $caption }}</figcaption>
    @endif
</figure>

@elseif($block->type === 'callout')
@php
    $style = $calloutStyles[$block->settings['style'] ?? 'info'] ?? $calloutStyles['info'];
    $ctitle = $block->settings['title'] ?? null;
@endphp
<div class="rounded-xl border {{ $style['border'] }} {{ $style['bg'] }} p-4">
    <div class="flex items-start gap-3">
        <span class="text-xl leading-none shrink-0">{{ $style['icon'] }}</span>
        <div class="min-w-0">
            @if($ctitle)<p class="font-bold {{ $style['text'] }} mb-1">{{ $ctitle }}</p>@endif
            <p class="text-sm {{ $style['text'] }} leading-relaxed whitespace-pre-wrap">{{ $block->content }}</p>
        </div>
    </div>
</div>

@elseif($block->type === 'quote')
@php $author = $block->settings['author'] ?? null; @endphp
<blockquote class="border-l-4 border-indigo-300 bg-indigo-50/50 rounded-r-xl px-5 py-4">
    <p class="text-gray-700 italic leading-relaxed">&ldquo;{{ $block->content }}&rdquo;</p>
    @if($author)<footer class="text-sm text-indigo-500 font-semibold mt-2">— {{ $author }}</footer>@endif
</blockquote>

@elseif($block->type === 'comparison')
@php
    $columns = $block->settings['columns'] ?? [];
    $colClass = match(min(count($columns), 4)) {
        2 => 'sm:grid-cols-2',
        3 => 'sm:grid-cols-3',
        4 => 'sm:grid-cols-4',
        default => 'sm:grid-cols-1',
    };
@endphp
@if($block->content)<p class="font-bold text-gray-800 mb-3">{{ $block->content }}</p>@endif
<div class="grid grid-cols-1 {{ $colClass }} gap-3">
    @foreach($columns as $col)
    <div class="rounded-xl overflow-hidden border border-gray-100">
        <div class="px-4 py-2.5 font-bold text-white text-sm" style="background: {{ $col['color'] ?? '#6366f1' }}">
            {{ $col['title'] ?? '' }}
        </div>
        <ul class="p-4 space-y-2 bg-white">
            @foreach($col['items'] ?? [] as $item)
            <li class="text-sm text-gray-600 flex items-start gap-2">
                <span class="shrink-0 mt-1.5 w-1.5 h-1.5 rounded-full" style="background: {{ $col['color'] ?? '#6366f1' }}"></span>
                <span>{{ $item }}</span>
            </li>
            @endforeach
        </ul>
    </div>
    @endforeach
</div>

@elseif($block->type === 'flashcards')
@php $cards = $block->settings['cards'] ?? []; @endphp
<div x-data="{ flipped: {} }">
    @if($block->content)<p class="font-bold text-gray-800 mb-3">{{ $block->content }}</p>@endif
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($cards as $ci => $card)
        <div class="relative h-36 cursor-pointer [perspective:1000px]" wire:key="flash-{{ $block->id }}-{{ $ci }}"
             @click="flipped[{{ $ci }}] = !flipped[{{ $ci }}]">
            <div class="relative w-full h-full transition-transform duration-500 [transform-style:preserve-3d]"
                 :class="flipped[{{ $ci }}] ? '[transform:rotateY(180deg)]' : ''">
                <div class="absolute inset-0 rounded-xl bg-cyan-50 border-2 border-cyan-200 flex items-center justify-center p-4 [backface-visibility:hidden]">
                    <p class="text-sm font-semibold text-cyan-800 text-center">{{ $card['front'] ?? '' }}</p>
                </div>
                <div class="absolute inset-0 rounded-xl bg-cyan-600 flex items-center justify-center p-4 [backface-visibility:hidden] [transform:rotateY(180deg)]">
                    <p class="text-sm font-medium text-white text-center">{{ $card['back'] ?? '' }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <p class="text-xs text-gray-400 mt-2 text-center">👆 Toque no cartão para virar</p>
</div>

@elseif($block->type === 'accordion')
@php $sections = $block->settings['items'] ?? []; @endphp
<div x-data="{ open: null }" class="space-y-2">
    @if($block->content)<p class="font-bold text-gray-800 mb-1">{{ $block->content }}</p>@endif
    @foreach($sections as $si => $sec)
    <div class="rounded-xl border border-gray-200 overflow-hidden" wire:key="acc-{{ $block->id }}-{{ $si }}">
        <button type="button" @click="open = open === {{ $si }} ? null : {{ $si }}"
                class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 text-left">
            <span class="text-sm font-semibold text-gray-800">{{ $sec['title'] ?? '' }}</span>
            <svg class="w-4 h-4 text-gray-400 transition-transform shrink-0" :class="open === {{ $si }} ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open === {{ $si }}" x-transition class="px-4 py-3 text-sm text-gray-600 leading-relaxed whitespace-pre-wrap">{{ $sec['body'] ?? '' }}</div>
    </div>
    @endforeach
</div>

@elseif($block->type === 'scale')
@php
    $minLabel = $block->settings['minLabel'] ?? null;
    $maxLabel = $block->settings['maxLabel'] ?? null;
    $current  = $interactive ? ($interactionAnswers[$block->id] ?? null) : null;
@endphp
<div class="rounded-xl border border-orange-200 bg-orange-50/50 p-5">
    <p class="font-semibold text-gray-800 mb-3">📊 {{ $block->content }}</p>
    <div class="flex flex-wrap gap-2">
        @for($n = 1; $n <= 10; $n++)
        @if($interactive)
        <button wire:click="selectScale({{ $block->id }}, {{ $n }})"
                class="w-9 h-9 rounded-lg text-sm font-bold transition-colors
                       {{ $current === $n ? 'bg-orange-500 text-white' : 'bg-white border border-orange-200 text-orange-600 hover:bg-orange-100' }}">
            {{ $n }}
        </button>
        @else
        <span class="w-9 h-9 rounded-lg text-sm font-bold bg-white border border-orange-200 text-orange-300 flex items-center justify-center">{{ $n }}</span>
        @endif
        @endfor
    </div>
    @if($minLabel || $maxLabel)
    <div class="flex justify-between text-xs text-orange-500 mt-2">
        <span>{{ $minLabel }}</span><span>{{ $maxLabel }}</span>
    </div>
    @endif
    @if($interactive && $current)
    <p class="text-xs text-emerald-600 font-semibold mt-2">✓ Resposta salva</p>
    @endif
    @if(!$interactive)
    <p class="text-xs text-gray-400 mt-2">Pré-visualização — o aluno poderá clicar em uma nota.</p>
    @endif
</div>

@elseif($block->type === 'reflection')
@php $current = $interactive ? ($interactionAnswers[$block->id] ?? '') : ''; @endphp
<div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-5">
    <p class="font-semibold text-gray-800 mb-3">✍️ {{ $block->content }}</p>
    @if($interactive)
    <textarea wire:model="interactionAnswers.{{ $block->id }}" rows="4" placeholder="Escreva sua resposta..."
              class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none resize-y"></textarea>
    <div class="flex items-center gap-3 mt-2">
        <button wire:click="saveInteraction({{ $block->id }})" class="px-4 py-2 text-sm font-semibold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white">
            Salvar resposta
        </button>
        @if(!empty($interactionSaved[$block->id]))
        <span class="text-xs text-emerald-600 font-semibold">✓ Salvo</span>
        @endif
    </div>
    @else
    <textarea rows="4" placeholder="O aluno escreverá a resposta aqui..." disabled
              class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm bg-white/50 text-gray-400 resize-none"></textarea>
    @endif
</div>
@endif
