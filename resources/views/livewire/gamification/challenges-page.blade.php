<div class="max-w-2xl mx-auto space-y-6 animate-fade-in">
    <h1 class="text-2xl font-bold text-gray-900 text-center">Desafios</h1>
    <p class="text-gray-500 text-center">Complete desafios para ganhar XP extra e badges especiais</p>

    {{-- Active Challenges --}}
    <div class="space-y-4">
        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Desafios Diarios</h2>
        @php
        $mockChallenges = [
            ['title' => 'Complete 3 aulas hoje', 'progress' => 1, 'target' => 3, 'xp' => 30, 'type' => 'daily', 'icon' => '📚'],
            ['title' => 'Acerte 10 questoes de quiz', 'progress' => 7, 'target' => 10, 'xp' => 25, 'type' => 'daily', 'icon' => '🎯'],
            ['title' => 'Estude por 30 minutos', 'progress' => 18, 'target' => 30, 'xp' => 20, 'type' => 'daily', 'icon' => '⏱️'],
        ];
        @endphp
        @foreach($mockChallenges as $ch)
        <div class="tu-card p-5 hover-scale">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-2xl shrink-0">{{ $ch['icon'] }}</div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">{{ $ch['title'] }}</h3>
                        <span class="text-sm font-bold" style="color: var(--tu-xp)">+{{ $ch['xp'] }} XP</span>
                    </div>
                    <div class="mt-3">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500">{{ $ch['progress'] }} de {{ $ch['target'] }}</span>
                            <span class="font-semibold" style="color: var(--tu-streak)">{{ number_format(($ch['progress'] / $ch['target']) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full transition-all" style="width: {{ ($ch['progress'] / $ch['target']) * 100 }}%; background: var(--tu-streak)"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider pt-4">Desafios Semanais</h2>
        @php
        $weeklyChallenges = [
            ['title' => 'Complete um curso inteiro', 'progress' => 0, 'target' => 1, 'xp' => 100, 'icon' => '🎓'],
            ['title' => 'Mantenha um streak de 5 dias', 'progress' => 3, 'target' => 5, 'xp' => 50, 'icon' => '🔥'],
            ['title' => 'Ganhe 3 badges', 'progress' => 1, 'target' => 3, 'xp' => 75, 'icon' => '🏅'],
        ];
        @endphp
        @foreach($weeklyChallenges as $ch)
        <div class="tu-card p-5 hover-scale">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-2xl shrink-0">{{ $ch['icon'] }}</div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">{{ $ch['title'] }}</h3>
                        <span class="text-sm font-bold" style="color: var(--tu-xp)">+{{ $ch['xp'] }} XP</span>
                    </div>
                    <div class="mt-3">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500">{{ $ch['progress'] }} de {{ $ch['target'] }}</span>
                            <span class="font-semibold text-purple-500">{{ number_format(($ch['progress'] / $ch['target']) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full transition-all bg-purple-500" style="width: {{ ($ch['progress'] / $ch['target']) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>