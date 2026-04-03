<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Crie sua conta</h2>
    <p class="text-gray-500 mb-8">Comece gratis por 14 dias. Sem cartao de credito.</p>

    <form wire:submit="register" class="space-y-5">
        <div>
            <label for="empresa" class="block text-sm font-medium text-gray-700 mb-1.5">Nome da Empresa</label>
            <input wire:model="empresa" type="text" id="empresa" placeholder="Sua empresa"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none text-gray-900 placeholder-gray-400">
            @error('empresa') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="nome" class="block text-sm font-medium text-gray-700 mb-1.5">Seu Nome</label>
            <input wire:model="nome" type="text" id="nome" placeholder="Seu nome completo"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none text-gray-900 placeholder-gray-400">
            @error('nome') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
            <input wire:model="email" type="email" id="email" placeholder="seu@email.com"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none text-gray-900 placeholder-gray-400">
            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="senha" class="block text-sm font-medium text-gray-700 mb-1.5">Senha</label>
                <input wire:model="senha" type="password" id="senha" placeholder="Min. 6 caracteres"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none text-gray-900 placeholder-gray-400">
                @error('senha') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="senha_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar</label>
                <input wire:model="senha_confirmation" type="password" id="senha_confirmation" placeholder="Confirme a senha"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none text-gray-900 placeholder-gray-400">
                @error('senha_confirmation') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <button type="submit" class="tu-btn tu-btn-primary tu-btn-lg w-full"
            wire:loading.attr="disabled" wire:loading.class="opacity-75">
            <span wire:loading.remove>Criar Conta Gratis</span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Criando...
            </span>
        </button>

        <p class="text-xs text-gray-400 text-center">
            Ao criar uma conta, voce concorda com nossos Termos de Uso e Politica de Privacidade.
        </p>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        Ja tem conta?
        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold" wire:navigate>
            Fazer login
        </a>
    </p>
</div>
