<div>
    @if($done)
    <div class="text-center py-6">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Senha redefinida!</h2>
        <p class="text-gray-500 text-sm mb-6">Sua nova senha foi salva com sucesso.</p>
        <a href="{{ route('login') }}" wire:navigate
           class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700
                  text-white font-bold transition-colors">
            Entrar na plataforma
        </a>
    </div>
    @else
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Criar nova senha</h2>
    <p class="text-gray-500 mb-8 text-sm">Escolha uma senha forte com pelo menos 8 caracteres.</p>

    @if($error)
    <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        {{ $error }}
    </div>
    @endif

    <form wire:submit="submit" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nova senha</label>
            <input wire:model="senha" type="password" placeholder="Mín. 8 caracteres"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white
                          focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all
                          text-gray-900 placeholder-gray-400">
            @error('senha') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar nova senha</label>
            <input wire:model="senha_confirmation" type="password" placeholder="Repita a senha"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white
                          focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all
                          text-gray-900 placeholder-gray-400">
        </div>

        <button type="submit"
                wire:loading.attr="disabled"
                class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold
                       transition-all disabled:opacity-60 flex items-center justify-center gap-2">
            <span wire:loading.remove>Salvar nova senha</span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Salvando...
            </span>
        </button>
    </form>
    @endif
</div>
