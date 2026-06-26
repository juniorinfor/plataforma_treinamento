@php
    $typeLabels = [
        'pdf'   => ['label' => 'PDF',          'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',                                                                                                                          'color' => '#EF4444'],
        'docx'  => ['label' => 'Word',          'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',                                                                                                                          'color' => '#3B82F6'],
        'doc'   => ['label' => 'Word',          'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',                                                                                                                          'color' => '#3B82F6'],
        'pptx'  => ['label' => 'Apresentação',  'icon' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z',                                                                                                                                                                 'color' => '#F97316'],
        'ppt'   => ['label' => 'Apresentação',  'icon' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z',                                                                                                                                                                 'color' => '#F97316'],
        'xlsx'  => ['label' => 'Planilha',      'icon' => 'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2',                                      'color' => '#10B981'],
        'xls'   => ['label' => 'Planilha',      'icon' => 'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2',                                      'color' => '#10B981'],
        'mp4'   => ['label' => 'Vídeo',         'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',                                                                                                           'color' => '#EC4899'],
        'webm'  => ['label' => 'Vídeo',         'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',                                                                                                           'color' => '#EC4899'],
        'mov'   => ['label' => 'Vídeo',         'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',                                                                                                           'color' => '#EC4899'],
    ];
    $getType = fn(?string $ft) => $typeLabels[$ft] ?? ['label' => strtoupper($ft ?? '?'), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => '#6B7280'];
    $sizeLabel = fn(int $kb) => $kb >= 1024 ? round($kb / 1024, 1) . ' MB' : $kb . ' KB';
@endphp

<div class="animate-fade-in space-y-6" x-data="{ viewMode: 'grid' }">

    {{-- ── Cabeçalho ──────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📚 Biblioteca</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manuais, e-books e materiais de apoio para o seu desenvolvimento</p>
        </div>
        <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1">
            <button @click="viewMode = 'grid'"
                    :class="viewMode === 'grid' ? 'bg-white shadow text-indigo-600' : 'text-gray-400 hover:text-gray-600'"
                    class="p-2 rounded-lg transition-all" title="Grade">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            </button>
            <button @click="viewMode = 'list'"
                    :class="viewMode === 'list' ? 'bg-white shadow text-indigo-600' : 'text-gray-400 hover:text-gray-600'"
                    class="p-2 rounded-lg transition-all" title="Lista">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>

    {{-- ── Busca ────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 bg-white shadow-sm focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100 transition-all">
        <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input wire:model.live.debounce.300ms="search" type="search"
               placeholder="Buscar por título ou descrição..."
               class="flex-1 bg-transparent text-gray-700 placeholder-gray-400 text-sm outline-none border-none">
    </div>

    {{-- ── Filtros por tipo ─────────────────────────────────────── --}}
    <div class="flex gap-2 flex-wrap">
        <button wire:click="$set('activeType', 'todos')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-all
                       {{ $activeType === 'todos' ? 'text-white shadow-md' : 'bg-white text-gray-500 border border-gray-200 hover:border-indigo-400 hover:text-indigo-600' }}"
                @style(['background: var(--tu-primary)' => $activeType === 'todos'])>
            Todos
            <span class="text-xs font-bold px-1.5 py-0.5 rounded-full {{ $activeType === 'todos' ? 'bg-white/25 text-white' : 'bg-gray-100 text-gray-500' }}">
                {{ $this->documents->count() }}
            </span>
        </button>

        @foreach($this->availableTypes as $ft)
        @php $ti = $getType($ft); @endphp
        <button wire:click="$set('activeType', '{{ $ft }}')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-all
                       {{ $activeType === $ft ? 'text-white shadow-md' : 'bg-white text-gray-500 border border-gray-200 hover:border-indigo-400 hover:text-indigo-600' }}"
                @style(['background: var(--tu-primary)' => $activeType === $ft])>
            {{ $ti['label'] }}
        </button>
        @endforeach
    </div>

    {{-- ── Recém adicionados ────────────────────────────────────── --}}
    @if($activeType === 'todos' && $search === '' && $this->recentDocuments->isNotEmpty())
    <div class="tu-card p-5">
        <div class="flex items-center gap-2 mb-4">
            <span>✨</span>
            <h2 class="text-sm font-semibold text-gray-800">Adicionados recentemente</h2>
            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">
                {{ $this->recentDocuments->count() }} recentes
            </span>
        </div>
        <div class="flex gap-3 overflow-x-auto pb-1">
            @foreach($this->recentDocuments as $doc)
            @php $ti = $getType($doc->file_type); @endphp
            <div class="flex-shrink-0 flex items-center gap-3 bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 min-w-[200px]">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $ti['color'] }}20">
                    <svg style="width:18px;height:18px;color:{{ $ti['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ti['icon'] }}"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-900 truncate max-w-[150px]">{{ $doc->title }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $doc->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Contador ────────────────────────────────────────────── --}}
    <p class="text-sm text-gray-500">
        Exibindo <span class="font-semibold text-gray-800">{{ $this->documents->count() }}</span> materiais
        @if($search) para "<em class="text-indigo-600">{{ $search }}</em>" @endif
    </p>

    {{-- ── Estado vazio ─────────────────────────────────────────── --}}
    @if($this->documents->isEmpty())
    <div class="tu-card text-center py-16">
        <div class="text-5xl mb-4">🔍</div>
        <h3 class="font-semibold text-gray-800 mb-2">Nenhum material encontrado</h3>
        <p class="text-sm text-gray-400 mb-5">Tente outros termos ou selecione outra categoria.</p>
        <button wire:click="$set('activeType', 'todos'); $set('search', '')"
                class="tu-btn tu-btn-primary">Limpar filtros</button>
    </div>
    @endif

    {{-- ══ Modo grade ══════════════════════════════════════════════ --}}
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($this->documents as $doc)
        @php $ti = $getType($doc->file_type); @endphp
        <div class="tu-card flex flex-col overflow-hidden">

            {{-- Cabeçalho colorido --}}
            <div class="px-5 pt-5 pb-4 flex items-start gap-4" style="background: {{ $ti['color'] }}0D">
                <div class="w-13 h-13 rounded-2xl flex items-center justify-center shrink-0 shadow-sm"
                     style="background: white; border: 2px solid {{ $ti['color'] }}30; width:52px;height:52px;">
                    <svg style="width:24px;height:24px;color:{{ $ti['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $ti['icon'] }}"/>
                    </svg>
                </div>
                <div class="flex-1 pt-1">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                          style="background: {{ $ti['color'] }}20; color: {{ $ti['color'] }}">
                        {{ $ti['label'] }}
                    </span>
                    <p class="text-[11px] text-gray-400 mt-2">{{ $doc->created_at->diffForHumans() }}</p>
                </div>
            </div>

            {{-- Corpo --}}
            <div class="px-5 py-4 flex-1 flex flex-col">
                <h3 class="font-semibold text-gray-900 text-sm leading-snug mb-2 line-clamp-2">{{ $doc->title }}</h3>
                @if($doc->description)
                <p class="text-xs text-gray-500 leading-relaxed line-clamp-3 flex-1">{{ $doc->description }}</p>
                @endif
            </div>

            {{-- Rodapé --}}
            <div class="px-5 py-4 border-t border-gray-50 flex items-center justify-between bg-gray-50/50">
                <div class="text-xs text-gray-400">
                    <span>{{ $sizeLabel($doc->file_size_kb ?? 0) }}</span>
                    @if($doc->version)
                    <span class="ml-2 px-1.5 py-0.5 bg-gray-100 rounded text-[10px]">v{{ $doc->version }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="download({{ $doc->id }})" wire:loading.attr="disabled"
                            class="flex items-center gap-1.5 tu-btn tu-btn-primary text-xs py-1.5 px-3 rounded-xl">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Baixar
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ══ Modo lista ══════════════════════════════════════════════ --}}
    <div x-show="viewMode === 'list'" class="flex flex-col gap-3">
        @foreach($this->documents as $doc)
        @php $ti = $getType($doc->file_type); @endphp
        <div class="tu-card flex items-center gap-4 px-5 py-4">

            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0"
                 style="background: {{ $ti['color'] }}18">
                <svg style="width:20px;height:20px;color:{{ $ti['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $ti['icon'] }}"/>
                </svg>
            </div>

            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-sm text-gray-900 truncate">{{ $doc->title }}</h3>
                <div class="flex items-center gap-2 text-xs text-gray-400 mt-0.5 flex-wrap">
                    <span class="font-semibold" style="color: {{ $ti['color'] }}">{{ $ti['label'] }}</span>
                    <span>·</span>
                    <span>{{ $sizeLabel($doc->file_size_kb ?? 0) }}</span>
                    <span>·</span>
                    <span>{{ $doc->created_at->diffForHumans() }}</span>
                </div>
            </div>

            @if($doc->description)
            <p class="hidden lg:block text-xs text-gray-400 w-64 truncate">{{ $doc->description }}</p>
            @endif

            <button wire:click="download({{ $doc->id }})" wire:loading.attr="disabled"
                    class="flex items-center gap-1.5 tu-btn tu-btn-primary text-xs py-2 px-3 rounded-xl shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Baixar
            </button>
        </div>
        @endforeach
    </div>

    <div class="h-4"></div>
</div>
