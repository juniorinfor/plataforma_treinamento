<div class="space-y-6 animate-fade-in">
    @php $authUser = auth()->user(); @endphp
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gerenciar Cursos</h1>
            @if($authUser->isPlatformAdmin())
            <p class="text-sm text-gray-500 mt-0.5">Todos os cursos da plataforma e empresas</p>
            @endif
        </div>
        <a href="{{ route('admin.courses.create') }}" wire:navigate class="tu-btn tu-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Novo Curso
        </a>
    </div>

    <div class="tu-card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Curso</th>
                    @if($authUser->isPlatformAdmin())
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Empresa</th>
                    @else
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Categoria</th>
                    @endif
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Inscritos</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($courses as $course)
                @php
                    $canEdit = $authUser->isPlatformAdmin()
                        || (!$course->is_platform_course && $course->company_id === $authUser->company_id);
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg overflow-hidden flex items-center justify-center text-white font-bold text-sm" style="background: {{ $course->category?->color ?? '#3B82F6' }}">
                                @if($course->thumbnail_path)
                                    <img src="{{ asset('storage/' . $course->thumbnail_path) }}" class="w-full h-full object-cover" alt="">
                                @else
                                    {{ substr($course->title, 0, 1) }}
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="font-medium text-gray-900">{{ $course->title }}</p>
                                    @if($course->is_platform_course)
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-indigo-50 text-indigo-600">Plataforma</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400">{{ $course->difficulty->label() }} · {{ $course->estimated_hours }}h</p>
                            </div>
                        </div>
                    </td>
                    @if($authUser->isPlatformAdmin())
                    <td class="px-5 py-4 text-sm text-gray-600">{{ $course->company?->name ?? '—' }}</td>
                    @else
                    <td class="px-5 py-4 text-sm text-gray-600">{{ $course->category?->name ?? '-' }}</td>
                    @endif
                    <td class="px-5 py-4 text-center text-sm font-medium text-gray-900">{{ $course->enrollments_count }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $course->is_published ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                            {{ $course->is_published ? 'Publicado' : 'Rascunho' }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right">
                        @if($canEdit)
                        <div class="inline-flex items-center gap-3">
                            <a href="{{ route('admin.courses.builder', $course->id) }}" wire:navigate
                               class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">Conteúdo</a>
                            <a href="{{ route('admin.courses.edit', $course->id) }}" wire:navigate
                               class="text-blue-600 hover:text-blue-700 text-sm font-medium">Editar</a>
                        </div>
                        @else
                        <span class="text-gray-300 text-sm">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>