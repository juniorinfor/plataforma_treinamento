<div>
    @if(!$company)
        {{-- Link inválido ou desativado --}}
        <div class="text-center py-4">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-1">Link indisponível</h2>
            <p class="text-gray-500 text-sm">
                Este link de cadastro é inválido ou foi desativado pelo gestor da empresa.
            </p>
            <a href="{{ route('login') }}" wire:navigate
               class="inline-block mt-6 text-blue-600 hover:text-blue-700 font-semibold text-sm">
                Ir para o login
            </a>
        </div>
    @else
        <h2 class="text-2xl font-bold text-gray-900 mb-1">Cadastro de colaborador</h2>
        <p class="text-gray-500 mb-6">
            Você está entrando em <strong class="text-gray-700">{{ $company->name }}</strong>.
        </p>

        <form wire:submit="register" class="space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Seu nome</label>
                <input wire:model="name" type="text" id="name" placeholder="Seu nome completo"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none text-gray-900 placeholder-gray-400">
                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">E-mail</label>
                <input wire:model="email" type="email" id="email" placeholder="seu@email.com"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none text-gray-900 placeholder-gray-400">
                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Senha</label>
                    <input wire:model="password" type="password" id="password" placeholder="Mín. 8 caracteres"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none text-gray-900 placeholder-gray-400">
                    @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar</label>
                    <input wire:model="password_confirmation" type="password" id="password_confirmation" placeholder="Repita a senha"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none text-gray-900 placeholder-gray-400">
                </div>
            </div>

            <button type="submit" class="tu-btn tu-btn-primary tu-btn-lg w-full"
                wire:loading.attr="disabled" wire:loading.class="opacity-75">
                <span wire:loading.remove>Criar minha conta</span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Criando...
                </span>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            Já tem conta?
            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold" wire:navigate>
                Fazer login
            </a>
        </p>
    @endif
</div>
