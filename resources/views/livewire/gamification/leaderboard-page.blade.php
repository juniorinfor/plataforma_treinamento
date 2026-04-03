<div class="max-w-2xl mx-auto space-y-6 animate-fade-in">
    <h1 class="text-2xl font-bold text-gray-900 text-center">Ranking</h1>

    {{-- Period Tabs --}}
    <div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit mx-auto">
        <button wire:click="$set('period', 'weekly')" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $period === 'weekly' ? 'bg-white shadow text-blue-600' : 'text-gray-500' }}">Semanal</button>
        <button wire:click="$set('period', 'monthly')" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $period === 'monthly' ? 'bg-white shadow text-blue-600' : 'text-gray-500' }}">Mensal</button>
        <button wire:click="$set('period', 'all_time')" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $period === 'all_time' ? 'bg-white shadow text-blue-600' : 'text-gray-500' }}">Geral</button>
    </div>

    {{-- Podium --}}
    @if($ranking->count() >= 3)
    <div class="flex items-end justify-center gap-4 pt-8 pb-4">
        {{-- 2nd Place --}}
        <div class="text-center animate-slide-up" style="animation-delay: 100ms">
            <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center text-white text-xl font-bold mb-2 ring-4 ring-gray-200">
                {{ substr($ranking[1]->user?->name ?? '?', 0, 2) }}
            </div>
            <p class="text-sm font-semibold text-gray-900 truncate max-w-[100px]">{{ $ranking[1]->user?->name ?? '-' }}</p>
            <p class="text-xs font-bold" style="color: var(--tu-xp)">{{ $ranking[1]->{$period === 'weekly' ? 'weekly_xp' : ($period === 'monthly' ? 'monthly_xp' : 'total_xp')} }} XP</p>
            <div class="mt-2 w-20 h-20 mx-auto tu-podium-2 rounded-t-lg flex items-center justify-center text-white text-2xl font-bold">2</div>
        </div>
        {{-- 1st Place --}}
        <div class="text-center animate-slide-up">
            <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-yellow-400 to-yellow-500 flex items-center justify-center text-white text-2xl font-bold mb-2 ring-4 ring-yellow-200">
                {{ substr($ranking[0]->user?->name ?? '?', 0, 2) }}
            </div>
            <p class="text-sm font-bold text-gray-900 truncate max-w-[120px]">{{ $ranking[0]->user?->name ?? '-' }}</p>
            <p class="text-xs font-bold" style="color: var(--tu-xp)">{{ $ranking[0]->{$period === 'weekly' ? 'weekly_xp' : ($period === 'monthly' ? 'monthly_xp' : 'total_xp')} }} XP</p>
            <div class="mt-2 w-24 h-28 mx-auto tu-podium-1 rounded-t-lg flex items-center justify-center text-white text-3xl font-bold">1</div>
        </div>
        {{-- 3rd Place --}}
        <div class="text-center animate-slide-up" style="animation-delay: 200ms">
            <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-orange-400 to-orange-500 flex items-center justify-center text-white text-xl font-bold mb-2 ring-4 ring-orange-200">
                {{ substr($ranking[2]->user?->name ?? '?', 0, 2) }}
            </div>
            <p class="text-sm font-semibold text-gray-900 truncate max-w-[100px]">{{ $ranking[2]->user?->name ?? '-' }}</p>
            <p class="text-xs font-bold" style="color: var(--tu-xp)">{{ $ranking[2]->{$period === 'weekly' ? 'weekly_xp' : ($period === 'monthly' ? 'monthly_xp' : 'total_xp')} }} XP</p>
            <div class="mt-2 w-20 h-16 mx-auto tu-podium-3 rounded-t-lg flex items-center justify-center text-white text-2xl font-bold">3</div>
        </div>
    </div>
    @endif

    {{-- Full Ranking List --}}
    <div class="tu-card divide-y divide-gray-50">
        @foreach($ranking as $i => $entry)
        <div class="flex items-center gap-4 p-4 {{ $entry->user_id === auth()->id() ? 'bg-blue-50' : '' }}">
            <span class="w-8 text-center font-bold text-sm {{ $i === 0 ? 'text-yellow-500' : ($i === 1 ? 'text-gray-400' : ($i === 2 ? 'text-orange-400' : 'text-gray-400')) }}">
                {{ $i + 1 }}
            </span>
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-400 flex items-center justify-center text-white text-sm font-bold">
                {{ substr($entry->user?->name ?? '?', 0, 2) }}
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900">{{ $entry->user?->name ?? 'Usuario' }}</p>
                <p class="text-xs text-gray-400">Nv. {{ $entry->currentLevel?->level_number ?? 1 }} - {{ $entry->currentLevel?->name ?? 'Novato' }}</p>
            </div>
            <span class="font-bold" style="color: var(--tu-xp)">{{ number_format($entry->{$period === 'weekly' ? 'weekly_xp' : ($period === 'monthly' ? 'monthly_xp' : 'total_xp')}) }} XP</span>
        </div>
        @endforeach
    </div>
</div>