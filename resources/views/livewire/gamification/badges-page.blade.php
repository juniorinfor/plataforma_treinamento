<div class="space-y-6 animate-fade-in">
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900">Conquistas</h1>
        <p class="text-gray-500 mt-1">{{ count($earnedIds) }} de {{ $badges->count() }} badges conquistados</p>
    </div>

    {{-- Progress --}}
    <div class="max-w-md mx-auto">
        <div class="tu-xp-bar h-3">
            <div class="tu-xp-bar-fill" style="width: {{ $badges->count() > 0 ? (count($earnedIds) / $badges->count()) * 100 : 0 }}%; background: linear-gradient(90deg, var(--tu-secondary), var(--tu-badge-epic))"></div>
        </div>
    </div>

    {{-- Badges Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 stagger-children">
        @foreach($badges as $badge)
        @php $earned = in_array($badge->id, $earnedIds); @endphp
        <div class="tu-badge-card {{ $earned ? 'earned' : 'locked' }} rarity-{{ $badge->rarity->value }} animate-slide-up hover-scale">
            <div class="w-16 h-16 rounded-full flex items-center justify-center text-3xl mb-3 {{ $earned ? '' : 'grayscale opacity-40' }}"
                 style="background: {{ $badge->color }}20;">
                @if($earned) 🏆 @else 🔒 @endif
            </div>
            <h3 class="font-bold text-gray-900 text-sm text-center {{ !$earned ? 'text-gray-400' : '' }}">{{ $badge->name }}</h3>
            <p class="text-xs text-gray-500 text-center mt-1">{{ $badge->description }}</p>
            <div class="flex items-center gap-2 mt-3">
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background: {{ $badge->rarity->color() }}20; color: {{ $badge->rarity->color() }}">
                    {{ $badge->rarity->label() }}
                </span>
                <span class="text-xs font-bold" style="color: var(--tu-xp)">+{{ $badge->xp_reward }} XP</span>
            </div>
        </div>
        @endforeach
    </div>
</div>