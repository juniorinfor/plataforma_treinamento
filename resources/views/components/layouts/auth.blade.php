<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Executive Map' }} - Executive Map</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <div class="min-h-screen flex">
        {{-- Left: Branding --}}
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 via-blue-700 to-purple-700 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="100" r="80" fill="white" opacity="0.1"/>
                    <circle cx="350" cy="300" r="120" fill="white" opacity="0.08"/>
                    <circle cx="200" cy="350" r="60" fill="white" opacity="0.12"/>
                </svg>
            </div>
            <div class="relative z-10 flex flex-col justify-center px-12 xl:px-20">
                <div class="mb-8">
                    {{-- Logo atual: TrainUp --}}
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="text-3xl font-extrabold text-white tracking-tight">Executive Map</span>
                    </div>
                    {{-- Logo alternativa: para usar, descomente abaixo e comente o bloco acima --}}
                    {{-- <div class="mb-6"><img src="{{ asset('images/logo_ad_singular_clara.png') }}" alt="Logo" class="h-14 w-auto"></div> --}}
                    <h1 class="text-4xl xl:text-5xl font-bold text-white leading-tight mb-4">
                        Transforme o treinamento da sua equipe
                    </h1>
                    <p class="text-lg text-blue-100 leading-relaxed">
                        Plataforma gamificada de universidade corporativa. Seus colaboradores aprendem, evoluem e se divertem.
                    </p>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 text-blue-100">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </div>
                        <span>Cursos e treinamentos personalizados</span>
                    </div>
                    <div class="flex items-center gap-3 text-blue-100">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </div>
                        <span>Gamificacao com XP, badges e ranking</span>
                    </div>
                    <div class="flex items-center gap-3 text-blue-100">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </div>
                        <span>Certificados automaticos e relatorios</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Form --}}
        <div class="flex-1 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                {{-- Mobile logo --}}
                {{-- Logo mobile atual: TrainUp --}}
                <div class="lg:hidden flex items-center gap-3 mb-8 justify-center">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-extrabold text-gray-900 tracking-tight">Executive Map</span>
                    {{-- Logo mobile alternativa: para usar, descomente abaixo e comente o bloco acima --}}
                    <div class="h-16 flex items-center px-5 border-b border-white/10"><img src="{{ asset('images/logo_ad_singular_clara.png') }}" alt="Logo" class="h-9 w-auto"></div>

                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
    @livewireScripts
</body>
</html>
