<div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Gerenciar Cursos</h1>
        <button class="tu-btn tu-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Novo Curso
        </button>
    </div>

    <div class="tu-card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Curso</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Categoria</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Inscritos</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Acoes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($courses as $course)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-sm" style="background: {{ $course->category?->color ?? '#3B82F6' }}">{{ substr($course->title, 0, 1) }}</div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $course->title }}</p>
                                <p class="text-xs text-gray-400">{{ $course->difficulty->label() }} · {{ $course->estimated_hours }}h</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-600">{{ $course->category?->name ?? '-' }}</td>
                    <td class="px-5 py-4 text-center text-sm font-medium text-gray-900">{{ $course->enrollments_count }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $course->is_published ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                            {{ $course->is_published ? 'Publicado' : 'Rascunho' }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">Editar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>