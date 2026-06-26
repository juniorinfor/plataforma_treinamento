<div class="space-y-6 animate-fade-in">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Usuários</h1>
            <p class="text-sm text-gray-500 mt-0.5">Todos os usuários da plataforma</p>
        </div>
        <button wire:click="openCreate"
                class="tu-btn tu-btn-primary flex items-center gap-1.5 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo usuário
        </button>
    </div>

    {{-- Senha criada --}}
    @if($showCreated)
    <div class="tu-card p-4 bg-emerald-50 border border-emerald-200 flex items-start gap-4">
        <div class="flex-1">
            <p class="font-semibold text-emerald-800">Usuário criado com sucesso!</p>
            <p class="text-sm text-emerald-700 mt-0.5">
                <strong>{{ $createdName }}</strong> ({{ $createdEmail }}) —
                Senha temporária: <code class="bg-white px-1.5 py-0.5 rounded font-mono text-emerald-900">{{ $createdPassword }}</code>
            </p>
        </div>
        <button wire:click="$set('showCreated', false)" class="text-emerald-400 hover:text-emerald-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    {{-- Filtros --}}
    <div class="flex flex-wrap gap-3">
        <input wire:model.live.debounce.300ms="search" type="text"
               placeholder="Buscar por nome ou e-mail..."
               class="flex-1 min-w-56 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
        <select wire:model.live="filterCompany"
                class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
            <option value="">Todas as empresas</option>
            <option value="none">Sem empresa (B2C)</option>
            @foreach($this->companies as $company)
            <option value="{{ $company->id }}">{{ $company->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterRole"
                class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
            <option value="">Todos os papéis</option>
            <option value="employee">Colaborador</option>
            <option value="manager">Gestor</option>
            <option value="company_admin">Admin Empresa</option>
            <option value="platform_admin">Admin Sistema</option>
        </select>
    </div>

    <div class="tu-card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Usuário</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Empresa</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Papel</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Matrículas</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($this->users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="font-semibold text-gray-900 text-sm">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-600">
                        {{ $user->company?->name ?? '—' }}
                    </td>
                    <td class="px-5 py-3 text-center">
                        @php
                        $roleColors = [
                            'employee'       => 'bg-gray-100 text-gray-600',
                            'manager'        => 'bg-blue-50 text-blue-700',
                            'company_admin'  => 'bg-indigo-50 text-indigo-700',
                            'platform_admin' => 'bg-purple-50 text-purple-700',
                        ];
                        $roleLabels = [
                            'employee'       => 'Colaborador',
                            'manager'        => 'Gestor',
                            'company_admin'  => 'Admin Empresa',
                            'platform_admin' => 'Admin Sistema',
                        ];
                        @endphp
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $roleColors[$user->role->value] ?? 'bg-gray-100 text-gray-500' }}">
                            {{ $roleLabels[$user->role->value] ?? $user->role->value }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center text-sm text-gray-700">{{ $user->enrollments_count }}</td>
                    <td class="px-5 py-3 text-center">
                        <button wire:click="toggleActive({{ $user->id }})"
                                class="text-xs font-bold px-2.5 py-1 rounded-full transition-colors
                                       {{ $user->is_active
                                           ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'
                                           : 'bg-red-50 text-red-600 hover:bg-red-100' }}">
                            {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                        </button>
                    </td>
                    <td class="px-5 py-3 text-right text-sm text-gray-400">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca acessou' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">
                        Nenhum usuário encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-50">
            {{ $this->users->links() }}
        </div>
    </div>

    {{-- Modal criação --}}
    @if($showCreate)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data x-on:keydown.escape.window="$wire.set('showCreate', false)">
        <div class="absolute inset-0 bg-black/40" wire:click="$set('showCreate', false)"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">
            <h3 class="text-lg font-bold text-gray-900">Novo usuário</h3>

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                    <input wire:model="createName" type="text"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    @error('createName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail *</label>
                    <input wire:model="createEmail" type="email"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    @error('createEmail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Papel *</label>
                    <select wire:model="createRole"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                        <option value="employee">Colaborador</option>
                        <option value="manager">Gestor</option>
                        <option value="company_admin">Admin Empresa</option>
                        <option value="platform_admin">Admin Sistema</option>
                    </select>
                    @error('createRole') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Empresa <span class="text-gray-400">(opcional — deixe vazio para B2C)</span></label>
                    <select wire:model="createCompany"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                        <option value="">— Sem empresa (B2C) —</option>
                        @foreach($this->companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button wire:click="$set('showCreate', false)"
                        class="tu-btn border border-gray-200 text-gray-600 text-sm px-4 py-2 rounded-xl">
                    Cancelar
                </button>
                <button wire:click="createUser"
                        wire:loading.attr="disabled"
                        class="tu-btn tu-btn-primary text-sm px-5 py-2 rounded-xl disabled:opacity-60">
                    <span wire:loading.remove>Criar usuário</span>
                    <span wire:loading>Criando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
