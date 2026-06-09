<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Bem-vindo de volta!</h2>
    <p class="text-gray-500 mb-8">Entre na sua conta para continuar aprendendo</p>

    @if($erro)
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2 animate-slide-down">
            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            {{ $erro }}
        </div>
    @endif

    <form wire:submit="login" class="space-y-5">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
            <input wire:model="email" type="email" id="email" placeholder="seu@email.com"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none text-gray-900 placeholder-gray-400">
            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="senha" class="block text-sm font-medium text-gray-700 mb-1.5">Senha</label>
            <input wire:model="senha" type="password" id="senha" placeholder="Sua senha"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none text-gray-900 placeholder-gray-400">
            @error('senha') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input wire:model="lembrar" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-600">Lembrar-me</span>
            </label>
            <a href="{{ route('password.request') }}" wire:navigate
               class="text-sm text-blue-600 hover:text-blue-700 font-medium">Esqueceu a senha?</a>
        </div>

        <button type="submit" class="tu-btn tu-btn-primary tu-btn-lg w-full"
            wire:loading.attr="disabled" wire:loading.class="opacity-75">
            <span wire:loading.remove>Entrar</span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Entrando...
            </span>
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-gray-500">
        Ainda nao tem conta?
        <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-semibold" wire:navigate>
            Registre sua empresa
        </a>
    </p>
</div>
