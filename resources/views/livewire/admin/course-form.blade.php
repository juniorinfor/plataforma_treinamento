<div class="max-w-3xl mx-auto space-y-6 animate-fade-in">

    {{-- Voltar --}}
    <a href="{{ route('admin.courses') }}" wire:navigate
       class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar aos cursos
    </a>

    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $courseId ? 'Editar curso' : 'Novo curso' }}</h1>
        <p class="text-gray-500 mt-1">Preencha as informações principais do curso.</p>
    </div>

    <form wire:submit="save" class="space-y-6">

        {{-- Capa / thumbnail --}}
        <div class="tu-card p-6">
            <label class="block text-sm font-semibold text-gray-700 mb-3">Imagem de capa</label>
            <div class="flex items-center gap-5">
                <div class="w-28 h-28 rounded-xl overflow-hidden shrink-0 flex items-center justify-center text-white text-3xl font-bold"
                     style="background: linear-gradient(135deg, #6366F1, #4338CA)">
                    @if($thumbnail)
                        <img src="{{ $thumbnail->temporaryUrl() }}" class="w-full h-full object-cover" alt="Prévia">
                    @elseif($existingThumbnail)
                        <img src="{{ asset('storage/' . $existingThumbnail) }}" class="w-full h-full object-cover" alt="Capa">
                    @else
                        {{ strtoupper(substr($title ?: 'C', 0, 1)) }}
                    @endif
                </div>
                <div class="flex-1">
                    <input type="file" wire:model="thumbnail" accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg
                                  file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600
                                  hover:file:bg-indigo-100 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-2">PNG ou JPG até 2MB. Sem imagem, usamos o gradiente padrão com a inicial.</p>
                    <div wire:loading wire:target="thumbnail" class="text-xs text-indigo-500 mt-1">Enviando imagem…</div>
                    @error('thumbnail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Dados principais --}}
        <div class="tu-card p-6 space-y-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Título *</label>
                <input type="text" wire:model="title" placeholder="Ex.: Segurança da Informação"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Descrição curta</label>
                <input type="text" wire:model="short_description" maxlength="255" placeholder="Uma frase que resume o curso"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                @error('short_description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Descrição completa</label>
                <textarea wire:model="description" rows="5" placeholder="Conteúdo, objetivos e público-alvo do curso"
                          class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none"></textarea>
                @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Categoria</label>
                    <select wire:model="category_id"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                        <option value="">— Sem categoria —</option>
                        @foreach($this->categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nível</label>
                    <select wire:model="difficulty"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                        @foreach($this->difficulties as $d)
                        <option value="{{ $d['value'] }}">{{ $d['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Carga horária (h)</label>
                    <input type="number" step="0.5" min="0" wire:model="estimated_hours"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                    @error('estimated_hours') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">XP de conclusão</label>
                <input type="number" min="0" wire:model="xp_reward"
                       class="w-40 rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
            </div>
        </div>

        {{-- Opções --}}
        <div class="tu-card p-6 space-y-4">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" wire:model="is_mandatory" class="w-4 h-4 rounded text-indigo-600">
                <span class="text-sm text-gray-700"><strong>Obrigatório</strong> — todos os colaboradores devem concluir</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" wire:model="is_published" class="w-4 h-4 rounded text-indigo-600">
                <span class="text-sm text-gray-700"><strong>Publicado</strong> — visível para os alunos (desmarcado = rascunho)</span>
            </label>
        </div>

        {{-- Ações --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.courses') }}" wire:navigate
               class="px-5 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="px-6 py-2.5 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white
                           disabled:opacity-60 flex items-center gap-2">
                <span wire:loading.remove wire:target="save">{{ $courseId ? 'Salvar alterações' : 'Criar curso' }}</span>
                <span wire:loading wire:target="save">Salvando…</span>
            </button>
        </div>
    </form>
</div>
