<div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Usuarios</h1>
        <button class="tu-btn tu-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Novo Usuario
        </button>
    </div>

    <div class="tu-card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Usuario</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Funcao</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">XP</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Nivel</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $u)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-purple-400 flex items-center justify-center text-white text-xs font-bold">{{ substr($u->name, 0, 2) }}</div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $u->name }}</p>
                                <p class="text-xs text-gray-400">{{ $u->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-600">{{ $u->role->label() }}</td>
                    <td class="px-5 py-4 text-center text-sm font-bold" style="color: var(--tu-xp)">{{ number_format($u->points?->total_xp ?? 0) }}</td>
                    <td class="px-5 py-4 text-center"><span class="tu-level-badge text-[10px]">Nv. {{ $u->points?->currentLevel?->level_number ?? 1 }}</span></td>
                    <td class="px-5 py-4 text-center">
                        <span class="w-2 h-2 inline-block rounded-full {{ $u->is_active ? 'bg-green-400' : 'bg-gray-300' }}"></span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>