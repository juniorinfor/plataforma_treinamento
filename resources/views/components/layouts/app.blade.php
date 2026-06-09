<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} - Executive Map</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-[var(--tu-bg)]" x-data="{ sidebarOpen: false }">

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden" style="display: none;"></div>

    {{-- Sidebar --}}
    <aside class="tu-sidebar fixed top-0 left-0 h-full w-64 z-50 flex flex-col transition-transform duration-300"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Logo --}}
        {{-- Sidebar logo atual: TrainUp --}}
        <div class="h-16 flex items-center gap-3 px-5 border-b border-white/10">
            <div class="w-9 h-9 bg-blue-500 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="text-lg font-bold text-white">Executive Map</span>
        </div>
        {{-- Sidebar logo alternativa: para usar, descomente abaixo e comente o bloco acima --}}
         <div class="h-16 flex items-center px-5 border-b border-white/10"><img src="{{ asset('images/logo_ad_singular_clara.png') }}" alt="Logo" class="h-9 w-auto"></div>

        {{-- User XP Mini --}}
        @auth
        <div class="px-4 py-3 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold text-sm">
                    {{ substr(auth()->user()->name, 0, 2) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="tu-level-badge text-[10px] py-0.5 px-2">
                            Nv. {{ auth()->user()->points?->currentLevel?->level_number ?? 1 }}
                        </span>
                        <span class="text-xs text-yellow-400 font-bold">{{ number_format(auth()->user()->total_xp) }} XP</span>
                    </div>
                </div>
            </div>
            {{-- XP Bar --}}
            <div class="tu-xp-bar mt-2">
                <div class="tu-xp-bar-fill" style="width: {{ min(auth()->user()->total_xp % 100, 100) }}%"></div>
            </div>
        </div>
        @endauth

        {{-- Navigation --}}
        @php $authUser = auth()->user(); @endphp
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            {{-- ══════════════════════════════════════════════
                 NÍVEL 1 — COLABORADOR
                 (visível para todos os usuários autenticados)
                 ══════════════════════════════════════════════ --}}

            <a href="{{ route('dashboard') }}"
               class="tu-sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('courses.index') }}"
               class="tu-sidebar-link {{ request()->routeIs('courses.*') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Cursos</span>
            </a>
            <a href="{{ route('progress-map') }}"
               class="tu-sidebar-link {{ request()->routeIs('progress-map') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                <span>Meu Progresso</span>
            </a>
            <a href="{{ route('diagnostics.index') }}"
               class="tu-sidebar-link {{ request()->routeIs('diagnostics.*') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Diagnósticos</span>
            </a>
            <a href="{{ route('library') }}"
               class="tu-sidebar-link {{ request()->routeIs('library') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                <span>Biblioteca</span>
            </a>

            <div class="pt-4 pb-2">
                <span class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Gamificação</span>
            </div>
            <a href="{{ route('leaderboard') }}"
               class="tu-sidebar-link {{ request()->routeIs('leaderboard') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Ranking</span>
            </a>
            <a href="{{ route('badges') }}"
               class="tu-sidebar-link {{ request()->routeIs('badges') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                <span>Conquistas</span>
            </a>
            <a href="{{ route('challenges') }}"
               class="tu-sidebar-link {{ request()->routeIs('challenges') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                <span>Desafios</span>
            </a>
            <a href="{{ route('certificates') }}"
               class="tu-sidebar-link {{ request()->routeIs('certificates') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                <span>Certificados</span>
            </a>
            <a href="{{ route('forum') }}"
               class="tu-sidebar-link {{ request()->routeIs('forum*') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                <span>Fórum</span>
            </a>

            {{-- ══════════════════════════════════════════════
                 NÍVEL 2 — GESTOR
                 (company_admin + manager)
                 ══════════════════════════════════════════════ --}}

            @if($authUser?->isGestor())
            <div class="pt-4 pb-2">
                <span class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Gestão da Empresa</span>
            </div>
            <a href="{{ route('admin.dashboard') }}"
               class="tu-sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Painel do Gestor</span>
            </a>
            <a href="{{ route('admin.users') }}"
               class="tu-sidebar-link {{ request()->routeIs('admin.users') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Colaboradores</span>
            </a>
            <a href="{{ route('admin.diagnostics') }}"
               class="tu-sidebar-link {{ request()->routeIs('admin.diagnostics') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Diagnósticos da Equipe</span>
            </a>
            <a href="{{ route('admin.courses') }}"
               class="tu-sidebar-link {{ request()->routeIs('admin.courses') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Gerenciar Cursos</span>
            </a>
            <a href="{{ route('admin.reports') }}"
               class="tu-sidebar-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Relatórios</span>
            </a>

            {{-- Billing — apenas Gestor com permissão de pagamento (company_admin) --}}
            @if($authUser?->canManageBilling())
            <div class="pt-4 pb-2">
                <span class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Assinatura</span>
            </div>
            <a href="{{ route('plans') }}"
               class="tu-sidebar-link {{ request()->routeIs('plans') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>Planos Empresariais</span>
            </a>
            <a href="{{ route('billing') }}"
               class="tu-sidebar-link {{ request()->routeIs('billing') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                <span>Faturamento</span>
            </a>
            @endif
            @endif

            {{-- ══════════════════════════════════════════════
                 NÍVEL 3 — ADMIN DO SISTEMA (platform_admin)
                 Desenvolvedor / configuração da plataforma
                 ══════════════════════════════════════════════ --}}

            @if($authUser?->isPlatformAdmin())
            <div class="pt-4 pb-2">
                <span class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin do Sistema</span>
            </div>
            <a href="{{ route('platform.diagnostics.index') }}"
               class="tu-sidebar-link {{ request()->routeIs('platform.diagnostics.index') ||
                                          request()->routeIs('platform.diagnostics.create') ||
                                          request()->routeIs('platform.diagnostics.edit') ||
                                          request()->routeIs('platform.diagnostics.questions') ? 'active' : '' }}"
               wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Ferramentas de Diagnóstico</span>
            </a>
            <a href="{{ route('platform.diagnostics.reports.index') }}"
               class="tu-sidebar-link {{ request()->routeIs('platform.diagnostics.reports.*') ? 'active' : '' }}"
               wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Fila de Relatórios</span>
                @php
                    $pendingReports = \App\Models\DiagnosticReport::whereIn('status', [
                        \App\Enums\DiagnosticReportStatus::Pending->value,
                        \App\Enums\DiagnosticReportStatus::AiGenerated->value,
                    ])->count();
                @endphp
                @if($pendingReports > 0)
                <span class="ml-auto text-[10px] font-bold bg-amber-500 text-white px-1.5 py-0.5 rounded-full">
                    {{ $pendingReports }}
                </span>
                @endif
            </a>
            <a href="{{ route('plans') }}"
               class="tu-sidebar-link {{ request()->routeIs('plans') ? 'active' : '' }}" wire:navigate>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>Planos da Plataforma</span>
            </a>
            @endif

        </nav>

        {{-- Logout --}}
        <div class="p-3 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="tu-sidebar-link w-full text-red-400 hover:bg-red-500/10 hover:text-red-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Sair</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="lg:ml-64 min-h-screen">
        {{-- Top Bar --}}
        <header class="sticky top-0 z-30 h-16 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center px-4 lg:px-6">
            {{-- Mobile menu --}}
            <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 rounded-lg hover:bg-gray-100">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div class="flex-1"></div>

            {{-- Top bar right --}}
            <div class="flex items-center gap-4">
                {{-- Streak --}}
                @auth
                <div class="tu-streak-fire {{ (auth()->user()->current_streak ?? 0) > 0 ? '' : 'inactive' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/></svg>
                    <span class="text-sm">{{ auth()->user()->current_streak ?? 0 }}</span>
                </div>

                {{-- Notifications --}}
                <button class="relative p-2 rounded-lg hover:bg-gray-100 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                @endauth
            </div>
        </header>

        {{-- Banner: assinatura com pagamento pendente (past_due) --}}
        @if(auth()->user()?->company?->subscription_status === \App\Enums\SubscriptionStatus::PastDue->value ||
            auth()->user()?->company?->subscription_status instanceof \App\Enums\SubscriptionStatus &&
            auth()->user()->company->subscription_status === \App\Enums\SubscriptionStatus::PastDue)
        <div class="bg-amber-500 text-white text-sm px-4 py-2.5 flex items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Pagamento pendente — regularize sua assinatura para evitar a suspensão do acesso.</span>
            </div>
            @if(auth()->user()?->canManageBilling())
            <a href="{{ route('billing') }}" wire:navigate
               class="shrink-0 font-bold underline hover:no-underline">Ver fatura →</a>
            @endif
        </div>
        @endif

        {{-- Flash: alerta de assinatura vindo do middleware --}}
        @if(session('subscription_alert'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 text-sm px-4 py-3 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('subscription_alert') }}
        </div>
        @endif

        {{-- Flash: erro genérico --}}
        @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 text-sm px-4 py-3 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- Flash: status / sucesso --}}
        @if(session('status'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-sm px-4 py-3 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            {{ session('status') }}
        </div>
        @endif

        {{-- Page Content --}}
        <main class="p-4 lg:p-6">
            {{ $slot }}
        </main>
    </div>

    {{-- ── Notificações de Gamificação ─────────────────────────────── --}}
    <div
        x-data="{
            toast: null,
            showToast(msg, color, icon) {
                this.toast = { msg, color, icon };
                setTimeout(() => this.toast = null, 4000);
            }
        }"
        @xp-earned.window="showToast(`+${ $event.detail.amount } XP`, '#F59E0B', '⭐')"
        @level-up.window="showToast('Você subiu de nível! 🎉', '#6366F1', '🚀')"
        @badge-earned.window="showToast(`Badge conquistado: ${ $event.detail.badge }`, '#10B981', '🏆')"
        class="fixed bottom-6 right-6 z-[100] pointer-events-none"
    >
        <div
            x-show="toast"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="flex items-center gap-3 bg-white rounded-2xl shadow-2xl border border-gray-100 px-5 py-3.5 min-w-[220px]"
            style="display: none"
        >
            <span x-text="toast?.icon" class="text-2xl"></span>
            <div>
                <p class="text-sm font-black" x-bind:style="'color:' + (toast?.color ?? '#374151')" x-text="toast?.msg"></p>
                <p class="text-xs text-gray-400 mt-0.5">Executive Map</p>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
