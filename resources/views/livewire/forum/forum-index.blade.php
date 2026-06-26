@php
    $authUser     = auth()->user();
    $userInitials = strtoupper(substr($authUser->name, 0, 2));
    $avatarColors = ['#6366f1','#10b981','#f59e0b','#ec4899','#8b5cf6','#14b8a6','#f97316','#06b6d4'];
    $avatarColor  = fn(int $id) => $avatarColors[$id % count($avatarColors)];
@endphp

<div class="animate-fade-in">

    {{-- ── Cabeçalho ──────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <span>💬</span> Fórum da Comunidade
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Tire dúvidas, compartilhe ideias e conecte-se com colegas</p>
        </div>
        <button wire:click="$set('showNewModal', true)"
                @if(!$authUser->company_id) disabled @endif
                class="tu-btn tu-btn-primary flex items-center gap-2 self-start sm:self-auto disabled:opacity-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Tópico
        </button>
    </div>

    @if(!$authUser->company_id)
    <div class="tu-card p-6 text-center text-gray-400 text-sm">
        Você não está vinculado a nenhuma empresa. O fórum estará disponível após a vinculação.
    </div>
    @else

    <div class="flex flex-col lg:flex-row gap-6">

        {{-- ── Lista de threads (coluna principal) ─────────────────── --}}
        <div class="flex-1 min-w-0">

            {{-- Filtros por categoria --}}
            <div class="flex flex-wrap gap-2 mb-5">
                <button wire:click="setCategory(null)"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium transition-all
                               {{ is_null($activeCategoryId) ? 'text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:border-indigo-400 hover:text-indigo-600' }}"
                        @style(['background: var(--tu-primary)' => is_null($activeCategoryId)])>
                    📋 Todos
                    <span class="ml-0.5 px-1.5 py-0.5 rounded-full text-xs font-semibold {{ is_null($activeCategoryId) ? 'bg-white/25 text-white' : 'bg-gray-100 text-gray-500' }}">
                        {{ $this->threads->count() }}
                    </span>
                </button>

                @foreach($this->categories as $cat)
                <button wire:click="setCategory({{ $cat->id }})"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium transition-all
                               {{ $activeCategoryId === $cat->id ? 'text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:border-indigo-400 hover:text-indigo-600' }}"
                        @style(['background: var(--tu-primary)' => $activeCategoryId === $cat->id])>
                    <span class="w-2 h-2 rounded-full shrink-0" style="background: {{ $cat->color }}"></span>
                    {{ $cat->name }}
                    <span class="ml-0.5 px-1.5 py-0.5 rounded-full text-xs font-semibold {{ $activeCategoryId === $cat->id ? 'bg-white/25 text-white' : 'bg-gray-100 text-gray-500' }}">
                        {{ $cat->threads_count }}
                    </span>
                </button>
                @endforeach
            </div>

            {{-- Threads --}}
            <div class="space-y-3">
                @forelse($this->threads as $thread)
                <div wire:click="openThread({{ $thread->id }})"
                     class="tu-card p-5 hover:shadow-md transition-all duration-200 cursor-pointer group
                            {{ $thread->is_pinned ? 'border-l-4' : '' }}"
                     @style(['border-left-color: var(--tu-primary)' => $thread->is_pinned])>
                    <div class="flex items-start gap-4">

                        {{-- Avatar --}}
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-sm"
                             style="background: {{ $avatarColor($thread->user_id) }}">
                            {{ strtoupper(substr($thread->user?->name ?? '?', 0, 2)) }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                @if($thread->is_pinned)
                                <span class="text-xs font-semibold text-indigo-600 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.293 2.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414L9.293 17.121a1 1 0 01-1.414-1.414L13.172 11H3a1 1 0 110-2h10.172L7.879 3.707a1 1 0 010-1.414z"/></svg>
                                    Fixado
                                </span>
                                @endif

                                @if($thread->category)
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                      style="background: {{ $thread->category->color }}20; color: {{ $thread->category->color }}">
                                    {{ $thread->category->name }}
                                </span>
                                @endif

                                @if($thread->posts_count >= 10)
                                <span class="text-xs font-semibold text-orange-500">🔥 Em alta</span>
                                @endif

                                @if($thread->is_locked)
                                <span class="text-xs font-semibold text-red-500">🔒 Bloqueado</span>
                                @endif
                            </div>

                            <h3 class="font-semibold text-gray-900 text-base leading-snug group-hover:text-indigo-600 transition-colors truncate">
                                {{ $thread->title }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-1">{{ $thread->content }}</p>

                            <div class="flex flex-wrap items-center gap-4 mt-2 text-xs text-gray-400">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    {{ $thread->user?->name ?? 'Desconhecido' }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $thread->last_post_at?->diffForHumans() ?? $thread->created_at->diffForHumans() }}
                                </span>
                                <span>💬 {{ $thread->posts_count }} {{ $thread->posts_count === 1 ? 'resposta' : 'respostas' }}</span>
                                <span>👁️ {{ $thread->views_count }} views</span>
                            </div>
                        </div>

                        <div class="hidden sm:flex items-center self-center text-gray-300 group-hover:text-indigo-400 transition-colors flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
                @empty
                <div class="tu-card text-center py-14">
                    <div class="text-5xl mb-3">💬</div>
                    <p class="text-gray-500 text-sm">
                        @if($activeCategoryId) Nenhum tópico nesta categoria ainda.
                        @else Nenhum tópico criado ainda. Seja o primeiro!
                        @endif
                    </p>
                    <button wire:click="$set('showNewModal', true)"
                            class="mt-4 tu-btn tu-btn-primary text-sm">
                        Criar primeiro tópico
                    </button>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── Sidebar direita ──────────────────────────────────────── --}}
        <div class="lg:w-64 flex-shrink-0 space-y-5">

            {{-- Top contribuidores --}}
            @if($this->topContributors->isNotEmpty())
            <div class="tu-card p-5">
                <h2 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span>🏆</span> Top Contribuidores
                </h2>
                <div class="space-y-3">
                    @foreach($this->topContributors as $i => $contributor)
                    <div class="flex items-center gap-3">
                        <div class="relative shrink-0">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm"
                                 style="background: {{ $avatarColor($contributor->id) }}">
                                {{ strtoupper(substr($contributor->name, 0, 2)) }}
                            </div>
                            @if($i < 3)
                            <span class="absolute -top-1 -right-1 text-xs leading-none">{{ ['🥇','🥈','🥉'][$i] }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $contributor->name }}</p>
                            <p class="text-xs text-gray-400">{{ $contributor->posts_count }} posts</p>
                        </div>
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                              style="background: var(--tu-primary)">
                            #{{ $i + 1 }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Categorias --}}
            @if($this->categories->isNotEmpty())
            <div class="tu-card p-5">
                <h2 class="font-bold text-gray-900 mb-3 flex items-center gap-2"><span>📁</span> Categorias</h2>
                <div class="space-y-1">
                    @foreach($this->categories as $cat)
                    <button wire:click="setCategory({{ $cat->id }})"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-colors hover:bg-gray-50
                                   {{ $activeCategoryId === $cat->id ? 'text-indigo-600 font-semibold bg-indigo-50' : 'text-gray-600' }}">
                        <span class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background: {{ $cat->color }}"></span>
                            {{ $cat->name }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                            {{ $cat->threads_count }}
                        </span>
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- CTA --}}
            <div class="rounded-2xl p-5" style="background: linear-gradient(135deg, var(--tu-primary) 0%, #818cf8 100%);">
                <p class="text-2xl mb-2">💡</p>
                <h3 class="font-bold text-white text-sm mb-1">Participe do Fórum!</h3>
                <p class="text-xs text-white/80 leading-relaxed mb-3">Compartilhe conhecimento e ajude seus colegas.</p>
                <button wire:click="$set('showNewModal', true)"
                        class="w-full bg-white/20 hover:bg-white/30 text-white text-xs font-semibold py-2 px-3 rounded-xl transition-colors">
                    Criar tópico
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══ MODAL — Ver thread ══════════════════════════════════════════ --}}
    @if($openedThreadId && $this->openedThread)
    @php $thread = $this->openedThread; @endphp
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);"
         x-data wire:click.self="$wire.closeThread()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[88vh] overflow-hidden flex flex-col">

            {{-- Header --}}
            <div class="flex items-start justify-between p-5 border-b border-gray-100 shrink-0">
                <div class="flex-1 min-w-0 pr-4">
                    <div class="flex flex-wrap gap-2 mb-1.5">
                        @if($thread->category)
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                              style="background: {{ $thread->category->color }}20; color: {{ $thread->category->color }}">
                            {{ $thread->category->name }}
                        </span>
                        @endif
                        @if($thread->is_pinned)
                        <span class="text-xs font-semibold text-indigo-500">📌 Fixado</span>
                        @endif
                        @if($thread->is_locked)
                        <span class="text-xs font-semibold text-red-500">🔒 Bloqueado</span>
                        @endif
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 leading-snug">{{ $thread->title }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Por <span class="font-medium">{{ $thread->user?->name }}</span>
                        · {{ $thread->created_at->diffForHumans() }}
                    </p>
                </div>
                <button wire:click="closeThread()" class="shrink-0 p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Corpo (scrollável) --}}
            <div class="overflow-y-auto flex-1 p-5 space-y-4">

                {{-- Post original --}}
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-xs shrink-0"
                         style="background: {{ $avatarColor($thread->user_id) }}">
                        {{ strtoupper(substr($thread->user?->name ?? '?', 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $thread->content }}</p>
                        </div>
                        <div class="mt-1 text-xs text-gray-400">{{ $thread->created_at->diffForHumans() }}</div>
                    </div>
                </div>

                {{-- Respostas --}}
                @foreach($thread->posts as $post)
                <div class="flex gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-xs shrink-0"
                         style="background: {{ $avatarColor($post->user_id) }}">
                        {{ strtoupper(substr($post->user?->name ?? '?', 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <div class="bg-white border border-gray-100 rounded-xl p-4">
                            <p class="text-xs font-semibold text-gray-900 mb-1">
                                {{ $post->user?->name ?? 'Usuário' }}
                                <span class="font-normal text-gray-400 ml-1">{{ $post->created_at->diffForHumans() }}</span>
                                @if($post->is_solution)
                                <span class="ml-2 text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">✓ Solução</span>
                                @endif
                            </p>
                            <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $post->content }}</p>
                        </div>
                    </div>
                </div>
                @endforeach

                @if($thread->posts->isEmpty())
                <p class="text-center text-sm text-gray-400 py-4">Nenhuma resposta ainda. Seja o primeiro a responder!</p>
                @endif
            </div>

            {{-- Resposta rápida --}}
            @if(!$thread->is_locked)
            <div class="border-t border-gray-100 p-4 shrink-0">
                <div class="flex gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-xs shrink-0"
                         style="background: var(--tu-primary)">
                        {{ $userInitials }}
                    </div>
                    <div class="flex-1">
                        <textarea wire:model="replyContent" rows="2"
                                  placeholder="Escreva sua resposta..."
                                  class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"></textarea>
                        @error('replyContent') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        <div class="flex justify-end mt-2">
                            <button wire:click="submitReply" wire:loading.attr="disabled"
                                    class="tu-btn tu-btn-primary text-sm disabled:opacity-60">
                                <span wire:loading.remove wire:target="submitReply">Responder</span>
                                <span wire:loading wire:target="submitReply">Enviando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="border-t border-gray-100 px-5 py-3 shrink-0">
                <p class="text-xs text-center text-red-500">🔒 Este tópico está bloqueado para novas respostas.</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ══ MODAL — Novo tópico ══════════════════════════════════════════ --}}
    @if($showNewModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);"
         x-data wire:click.self="$wire.set('showNewModal', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Criar Novo Tópico</h2>
                <button wire:click="$set('showNewModal', false)" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                    <input wire:model="newTitle" type="text" placeholder="Descreva brevemente sua questão..."
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    @error('newTitle') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoria *</label>
                    <select wire:model="newCategoryId" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <option value="">Selecione uma categoria</option>
                        @foreach($this->categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('newCategoryId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conteúdo *</label>
                    <textarea wire:model="newContent" rows="5" placeholder="Descreva sua questão em detalhes..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"></textarea>
                    @error('newContent') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex gap-3 pt-1">
                    <button wire:click="$set('showNewModal', false)"
                            class="tu-btn flex-1 border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm py-2.5 rounded-xl">
                        Cancelar
                    </button>
                    <button wire:click="createThread" wire:loading.attr="disabled"
                            class="tu-btn tu-btn-primary flex-1 text-sm py-2.5 rounded-xl disabled:opacity-60">
                        <span wire:loading.remove wire:target="createThread">Publicar Tópico</span>
                        <span wire:loading wire:target="createThread">Publicando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
