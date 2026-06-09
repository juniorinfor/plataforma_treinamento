<div>
    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar para o login
    </a>

    @if($sent)
    <div class="text-center py-6">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Verifique seu e-mail</h2>
        <p class="text-gray-500 text-sm leading-relaxed">
            Se o e-mail <strong>{{ $email }}</strong> estiver cadastrado, você receberá um link para redefinir sua senha em instantes.
        </p>
        <p class="text-xs text-gray-400 mt-4">Não recebeu? Verifique a pasta de spam ou tente novamente.</p>
        <button wire:click="$set('sent', false)"
                class="mt-4 text-sm text-blue-600 hover:underline font-medium">
            Tentar outro e-mail
        </button>
    </div>
    @else
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Esqueceu sua senha?</h2>
    <p class="text-gray-500 mb-8 text-sm">Informe seu e-mail e enviaremos um link para criar uma nova senha.</p>

    <form wire:submit="send" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">E-mail</label>
            <input wire:model="email" type="email" placeholder="seu@email.com" autofocus
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white
                          focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all
                          text-gray-900 placeholder-gray-400">
            @error('email')
            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit"
                wire:loading.attr="disabled"
                class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold
                       transition-all disabled:opacity-60 flex items-center justify-center gap-2">
            <span wire:loading.remove>Enviar link de recuperação</span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Enviando...
            </span>
        </button>
    </form>
    @endif
</div>
