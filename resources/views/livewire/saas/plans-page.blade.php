<div class="max-w-5xl mx-auto space-y-8 animate-fade-in">
    <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-900">Planos TrainUp</h1>
        <p class="text-gray-500 mt-2">Escolha o plano ideal para sua empresa</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 stagger-children">
        @foreach($plans as $i => $plan)
        <div class="tu-card p-6 animate-slide-up {{ $i === 1 ? 'ring-2 ring-blue-500 relative' : '' }}">
            @if($i === 1)
            <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 bg-blue-500 text-white text-xs font-bold rounded-full">MAIS POPULAR</span>
            @endif
            <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $plan->description }}</p>
            <div class="mt-4">
                <span class="text-4xl font-extrabold text-gray-900">R${{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                <span class="text-gray-400">/mes</span>
            </div>
            <p class="text-xs text-gray-400 mt-1">ou R${{ number_format($plan->price_yearly, 0, ',', '.') }}/ano</p>

            <ul class="mt-6 space-y-3">
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Ate {{ $plan->max_users > 9999 ? 'ilimitados' : $plan->max_users }} usuarios
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    {{ $plan->max_courses > 9999 ? 'Cursos ilimitados' : $plan->max_courses . ' cursos' }}
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    {{ number_format($plan->max_storage_mb / 1024, 0) }}GB armazenamento
                </li>
                @if($plan->features)
                @foreach(array_slice($plan->features, 0, 3) as $feature)
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    {{ ucfirst(str_replace('_', ' ', $feature)) }}
                </li>
                @endforeach
                @endif
            </ul>

            <button class="tu-btn w-full mt-6 {{ $i === 1 ? 'tu-btn-primary' : 'tu-btn-outline' }}">
                Escolher {{ $plan->name }}
            </button>
        </div>
        @endforeach
    </div>
</div>