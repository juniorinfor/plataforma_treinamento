<div class="space-y-6 animate-fade-in">
    <h1 class="text-2xl font-bold text-gray-900">Painel Administrativo</h1>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 stagger-children">
        <div class="tu-card p-5 animate-slide-up">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total de Usuarios</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalUsers }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <p class="text-xs text-green-500 mt-2 font-medium">+3 esta semana</p>
        </div>
        <div class="tu-card p-5 animate-slide-up">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Cursos Ativos</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalCourses }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
            </div>
        </div>
        <div class="tu-card p-5 animate-slide-up">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Inscricoes</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalEnrollments }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
            </div>
        </div>
        <div class="tu-card p-5 animate-slide-up">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Taxa de Conclusao</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $completionRate }}%</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <p class="text-xs text-green-500 mt-2 font-medium">+5% vs mes anterior</p>
        </div>
    </div>

    {{-- Charts Placeholder --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="tu-card p-6">
            <h3 class="font-bold text-gray-900 mb-4">Atividade Semanal</h3>
            <div class="flex items-end gap-2 h-40">
                @php $bars = [40, 65, 55, 80, 45, 90, 70]; $days = ['Seg','Ter','Qua','Qui','Sex','Sab','Dom']; @endphp
                @foreach($bars as $i => $h)
                <div class="flex-1 flex flex-col items-center gap-1">
                    <div class="w-full rounded-t-lg transition-all animate-slide-up" style="height: {{ $h }}%; background: var(--tu-primary); opacity: {{ 0.5 + ($h/200) }}; animation-delay: {{ $i * 50 }}ms"></div>
                    <span class="text-[10px] text-gray-400">{{ $days[$i] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        <div class="tu-card p-6">
            <h3 class="font-bold text-gray-900 mb-4">Top Cursos</h3>
            <div class="space-y-4">
                @php $topCourses = [['name' => 'Seguranca da Informacao', 'enrollments' => 45, 'max' => 45], ['name' => 'Onboarding TechCorp', 'enrollments' => 38, 'max' => 45], ['name' => 'Lideranca e Gestao', 'enrollments' => 22, 'max' => 45]]; @endphp
                @foreach($topCourses as $tc)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700">{{ $tc['name'] }}</span>
                        <span class="text-gray-500">{{ $tc['enrollments'] }} inscritos</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full" style="width: {{ ($tc['enrollments']/$tc['max'])*100 }}%; background: var(--tu-primary)"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>