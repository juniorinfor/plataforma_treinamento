<div class="space-y-5 animate-fade-in">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Colaboradores</h1>
            <p class="text-sm text-gray-500 mt-0.5">Todos os membros vinculados à sua empresa</p>
        </div>
        <button wire:click="openInvite"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700
                       text-white text-sm font-semibold transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Convidar colaborador
        </button>
    </div>

    {{-- ── Filtros ── --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text"
                   placeholder="Buscar por nome ou e-mail..."
                   class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl
                          focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
        <select wire:model.live="filterDept"
                class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-white
                       focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="">Todos os departamentos</option>
            @foreach($this->departments as $d)
            <option value="{{ $d->id }}">{{ $d->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterRole"
                class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-white
                       focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="">Todos os níveis</option>
            @foreach($this->roleOptions() as $role)
            <option value="{{ $role->value }}">{{ $role->label() }}</option>
            @endforeach
        </select>
    </div>

    {{-- ── Tabela ── --}}
    <div class="tu-card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Colaborador</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Nível</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">XP</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Cursos</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Diagnóstico</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($this->users as $u)
                @php
                    $lastDiag = $u->latestDiagnosticAssessment;
                    $score    = $lastDiag ? (float) $lastDiag->global_score : null;
                    $lcolor   = $score !== null ? match(true) {
                        $score >= 85 => '#10B981',
                        $score >= 70 => '#3B82F6',
                        $score >= 55 => '#F59E0B',
                        $score >= 40 => '#EF4444',
                        default      => '#6B7280',
                    } : null;
                    $completed = $u->enrollments->whereNotNull('completed_at')->count();
                    $total     = $u->enrollments->count();
                @endphp
                <tr class="hover:bg-gray-50 transition-colors" wire:key="user-{{ $u->id }}">

                    {{-- Nome + e-mail + papel --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-purple-400
                                        flex items-center justify-center text-white text-xs font-bold shrink-0">
                                {{ strtoupper(substr($u->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-gray-900">{{ $u->name }}</p>
                                <p class="text-xs text-gray-400">{{ $u->email }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Nível do papel --}}
                    <td class="px-5 py-3.5 hidden sm:table-cell">
                        <span class="text-xs font-semibold px-2 py-1 rounded-full
                            {{ $u->role === \App\Enums\UserRole::CompanyAdmin
                                ? 'bg-indigo-100 text-indigo-700'
                                : ($u->role === \App\Enums\UserRole::Manager
                                    ? 'bg-purple-100 text-purple-700'
                                    : 'bg-gray-100 text-gray-600') }}">
                            {{ $u->role?->label() ?? '—' }}
                        </span>
                    </td>

                    {{-- XP --}}
                    <td class="px-5 py-3.5 text-center hidden sm:table-cell">
                        <span class="text-sm font-black text-yellow-600">
                            {{ number_format($u->points?->total_xp ?? 0) }}
                        </span>
                    </td>

                    {{-- Cursos --}}
                    <td class="px-5 py-3.5 text-center hidden lg:table-cell">
                        <span class="text-sm text-gray-700 font-semibold">{{ $completed }}</span>
                        <span class="text-xs text-gray-400">/{{ $total }}</span>
                    </td>

                    {{-- Último diagnóstico --}}
                    <td class="px-5 py-3.5 text-center">
                        @if($lastDiag && $score !== null)
                        <div class="flex flex-col items-center gap-0.5">
                            <span class="text-base font-black" style="color:{{ $lcolor }}">
                                {{ number_format($score, 0) }}
                            </span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full"
                                  style="background:{{ $lcolor }}18; color:{{ $lcolor }}">
                                {{ $lastDiag->global_label }}
                            </span>
                        </div>
                        @else
                        <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>

                    {{-- Status ativo --}}
                    <td class="px-5 py-3.5 text-center hidden sm:table-cell">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium
                            {{ $u->is_active ? 'text-emerald-600' : 'text-gray-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $u->is_active ? 'bg-emerald-400' : 'bg-gray-300' }}"></span>
                            {{ $u->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>

                    {{-- Ações --}}
                    <td class="px-5 py-3.5 text-right">
                        @if($u->id !== auth()->id())
                        <button wire:click="toggleActive({{ $u->id }})"
                                wire:confirm="{{ $u->is_active ? 'Desativar este usuário?' : 'Reativar este usuário?' }}"
                                class="text-xs font-semibold {{ $u->is_active ? 'text-red-400 hover:text-red-600' : 'text-emerald-500 hover:text-emerald-700' }} transition-colors">
                            {{ $u->is_active ? 'Desativar' : 'Reativar' }}
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">
                        @if($search)
                            Nenhum colaborador encontrado para "<strong>{{ $search }}</strong>".
                        @else
                            Nenhum colaborador cadastrado. Clique em <strong>Convidar colaborador</strong> para começar.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($this->users->hasPages())
        <div class="px-5 py-3 border-t border-gray-50">
            {{ $this->users->links() }}
        </div>
        @endif
    </div>

    {{-- ════════════════════════════════════
         MODAL — Convidar colaborador
         ════════════════════════════════════ --}}
    @if($showInvite)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
         wire:click.self="$set('showInvite', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-5">

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Convidar colaborador</h2>
                <button wire:click="$set('showInvite', false)"
                        class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                {{-- Nome --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome completo</label>
                    <input wire:model="inviteName" type="text" placeholder="João da Silva"
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-indigo-300 transition-all">
                    @error('inviteName') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- E-mail --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input wire:model="inviteEmail" type="email" placeholder="joao@empresa.com.br"
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-indigo-300 transition-all">
                    @error('inviteEmail') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Nível de acesso --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nível de acesso</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($this->roleOptions() as $role)
                        <label class="flex flex-col items-center gap-1 p-3 rounded-xl border-2 cursor-pointer transition-all
                                      {{ $inviteRole === $role->value
                                          ? 'border-indigo-500 bg-indigo-50'
                                          : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" wire:model="inviteRole" value="{{ $role->value }}" class="sr-only">
                            <span class="text-xs font-bold text-gray-700">{{ $role->label() }}</span>
                            <span class="text-[10px] text-gray-400 text-center leading-tight">
                                @if($role->value === 'employee') Cursos e diagnósticos
                                @elseif($role->value === 'company_admin') Gestão + billing
                                @else Gestão da equipe
                                @endif
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Departamento (opcional) --}}
                @if($this->departments->isNotEmpty())
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Departamento <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <select wire:model="inviteDept"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm bg-white
                                   focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <option value="">Sem departamento</option>
                        @foreach($this->departments as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Enviar e-mail --}}
                <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl bg-gray-50 border border-gray-200">
                    <input type="checkbox" wire:model="sendEmail"
                           class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Enviar convite por e-mail</p>
                        <p class="text-xs text-gray-400">O colaborador receberá as credenciais de acesso</p>
                    </div>
                </label>
            </div>

            <div class="flex gap-3 pt-1">
                <button wire:click="$set('showInvite', false)"
                        class="flex-1 py-2.5 rounded-xl border-2 border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                    Cancelar
                </button>
                <button wire:click="createUser"
                        wire:loading.attr="disabled"
                        wire:target="createUser"
                        class="flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold
                               transition-colors disabled:opacity-60">
                    <span wire:loading.remove wire:target="createUser">Criar usuário</span>
                    <span wire:loading wire:target="createUser">Criando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════
         MODAL — Usuário criado (mostra senha)
         ════════════════════════════════════ --}}
    @if($showCreated)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center space-y-4">
            <div class="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center mx-auto">
                <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Usuário criado!</h2>
            <p class="text-sm text-gray-500">
                <strong>{{ $createdName }}</strong> foi adicionado(a) com sucesso.
                @if($sendEmail)
                Um e-mail com as credenciais foi enviado para <strong>{{ $createdEmail }}</strong>.
                @else
                Compartilhe as credenciais abaixo manualmente.
                @endif
            </p>

            <div class="bg-gray-50 rounded-xl p-4 text-left space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">E-mail</span>
                    <span class="font-semibold text-gray-800">{{ $createdEmail }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Senha temporária</span>
                    <span class="font-black text-indigo-600 font-mono tracking-wider">{{ $createdPassword }}</span>
                </div>
            </div>

            <p class="text-xs text-amber-600 bg-amber-50 rounded-lg px-3 py-2">
                ⚠️ Anote a senha — ela não será exibida novamente.
            </p>

            <button wire:click="$set('showCreated', false)"
                    class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm transition-colors">
                Entendido
            </button>
        </div>
    </div>
    @endif

</div>
