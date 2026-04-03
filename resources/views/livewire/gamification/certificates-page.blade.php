<div class="max-w-3xl mx-auto space-y-6 animate-fade-in">
    <h1 class="text-2xl font-bold text-gray-900 text-center">Meus Certificados</h1>

    @if($certificates->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($certificates as $cert)
        <div class="tu-card p-6 hover-lift">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-yellow-400 to-yellow-500 flex items-center justify-center text-white text-2xl">🎓</div>
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $cert->course->title }}</h3>
                    <p class="text-xs text-gray-500">Emitido em {{ $cert->issued_at->format('d/m/Y') }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Codigo: {{ $cert->code }}</p>
                </div>
            </div>
            <button class="tu-btn tu-btn-outline w-full mt-4 text-sm">Baixar PDF</button>
        </div>
        @endforeach
    </div>
    @else
    {{-- Mock certificates --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="tu-card p-6 hover-lift">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-yellow-400 to-yellow-500 flex items-center justify-center text-white text-2xl">🎓</div>
                <div>
                    <h3 class="font-semibold text-gray-900">Seguranca da Informacao</h3>
                    <p class="text-xs text-gray-500">Emitido em 20/03/2026</p>
                    <p class="text-xs text-gray-400 mt-0.5">Codigo: TU-X8K9M2</p>
                </div>
            </div>
            <button class="tu-btn tu-btn-outline w-full mt-4 text-sm">Baixar PDF</button>
        </div>
        <div class="tu-card p-6 border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-center py-10">
            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            <p class="text-sm text-gray-400">Complete mais cursos para ganhar certificados!</p>
        </div>
    </div>
    @endif
</div>