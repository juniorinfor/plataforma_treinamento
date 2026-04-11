<div
    x-data="{
        activeType: 'todos',
        searchQuery: '',
        viewMode: 'grid',
        materials: [
            { id: 1, title: 'Manual de Integração de Novos Colaboradores', description: 'Guia completo para o processo de onboarding: cultura, sistemas, fluxos internos e primeiros passos na empresa.', type: 'manual', typeLabel: 'Manual', tags: ['Onboarding', 'RH'], size: '3.2 MB', pages: 48, duration: null, isNew: true, downloadable: true, addedDate: 'Há 3 dias', accent: '#3B82F6' },
            { id: 2, title: 'E-book: Liderança Situacional na Prática', description: 'Aprenda a adaptar seu estilo de liderança conforme o nível de maturidade de cada colaborador da equipe.', type: 'ebook', typeLabel: 'E-book', tags: ['Liderança', 'Gestão'], size: '5.7 MB', pages: 112, duration: null, isNew: true, downloadable: true, addedDate: 'Há 5 dias', accent: '#8B5CF6' },
            { id: 3, title: 'Procedimentos de Segurança do Trabalho', description: 'Normas e procedimentos obrigatórios de segurança, EPIs por área e plano de emergência da unidade.', type: 'manual', typeLabel: 'Manual', tags: ['Segurança', 'NR'], size: '8.1 MB', pages: 95, duration: null, isNew: false, downloadable: true, addedDate: 'Há 1 mês', accent: '#EF4444' },
            { id: 4, title: 'Estratégia de Vendas 2025 — Apresentação Executiva', description: 'Análise de mercado, metas anuais, segmentação de clientes e principais iniciativas da área comercial.', type: 'apresentacao', typeLabel: 'Apresentação', tags: ['Vendas', 'Estratégia'], size: '12.4 MB', pages: 34, duration: null, isNew: true, downloadable: false, addedDate: 'Há 1 semana', accent: '#F59E0B' },
            { id: 5, title: 'E-book: Comunicação Não-Violenta no Trabalho', description: 'Técnicas de CNV para melhorar relacionamentos profissionais, resolver conflitos e dar feedbacks construtivos.', type: 'ebook', typeLabel: 'E-book', tags: ['Comunicação', 'Soft Skills'], size: '4.3 MB', pages: 88, duration: null, isNew: false, downloadable: true, addedDate: 'Há 2 semanas', accent: '#10B981' },
            { id: 6, title: 'Webinar: Excel Avançado para Análise de Dados', description: 'Gravação do webinar sobre Power Query, tabelas dinâmicas, fórmulas avançadas e dashboards no Excel.', type: 'video', typeLabel: 'Vídeo', tags: ['Excel', 'Dados'], size: '1.2 GB', pages: null, duration: '1h 48min', isNew: false, downloadable: false, addedDate: 'Há 3 semanas', accent: '#EC4899' },
            { id: 7, title: 'Manual de Processos — Departamento Financeiro', description: 'Fluxos de aprovação, prazos, conciliação bancária, fechamento mensal e políticas de reembolso.', type: 'manual', typeLabel: 'Manual', tags: ['Financeiro', 'Processos'], size: '6.8 MB', pages: 76, duration: null, isNew: false, downloadable: true, addedDate: 'Há 1 mês', accent: '#06B6D4' },
            { id: 8, title: 'Onboarding Tech — Guia de Ferramentas e Ambientes', description: 'Configuração do ambiente de desenvolvimento, acesso a sistemas, repositórios e boas práticas de código.', type: 'manual', typeLabel: 'Manual', tags: ['TI', 'Dev'], size: '2.9 MB', pages: 52, duration: null, isNew: true, downloadable: true, addedDate: 'Há 4 dias', accent: '#6366F1' },
            { id: 9, title: 'Workshop: Design Thinking e Inovação', description: 'Gravação do workshop sobre metodologia de Design Thinking aplicada a desafios reais de negócio.', type: 'video', typeLabel: 'Vídeo', tags: ['Inovação', 'Workshop'], size: '980 MB', pages: null, duration: '2h 15min', isNew: false, downloadable: false, addedDate: 'Há 2 meses', accent: '#D946EF' },
            { id: 10, title: 'Resultados Q1 2025 — Revisão Trimestral', description: 'Apresentação dos resultados do primeiro trimestre: KPIs, OKRs, análise de performance e próximos passos.', type: 'apresentacao', typeLabel: 'Apresentação', tags: ['Resultados', 'OKR'], size: '7.5 MB', pages: 28, duration: null, isNew: false, downloadable: false, addedDate: 'Há 2 semanas', accent: '#14B8A6' }
        ],
        typeIcons: {
            manual:       'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            ebook:        'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            apresentacao: 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z',
            video:        'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'
        },
        get filteredMaterials() {
            let list = this.materials;
            if (this.activeType !== 'todos') list = list.filter(m => m.type === this.activeType);
            if (this.searchQuery.trim()) {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(m =>
                    m.title.toLowerCase().includes(q) ||
                    m.description.toLowerCase().includes(q) ||
                    m.tags.some(t => t.toLowerCase().includes(q))
                );
            }
            return list;
        },
        typeCount(key) { return key === 'todos' ? this.materials.length : this.materials.filter(m => m.type === key).length; }
    }"
    class="animate-fade-in space-y-6"
>

    {{-- ── CABEÇALHO ─────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[var(--tu-text)]">📚 Biblioteca</h1>
            <p class="text-sm text-[var(--tu-text-secondary)] mt-1">
                Manuais, e-books e materiais de apoio para o seu desenvolvimento
            </p>
        </div>
        {{-- Toggle grade / lista --}}
        <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1">
            <button @click="viewMode = 'grid'"
                :class="viewMode === 'grid' ? 'bg-white shadow text-[var(--tu-primary)]' : 'text-gray-400 hover:text-gray-600'"
                class="p-2 rounded-lg transition-all" title="Grade">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </button>
            <button @click="viewMode = 'list'"
                :class="viewMode === 'list' ? 'bg-white shadow text-[var(--tu-primary)]' : 'text-gray-400 hover:text-gray-600'"
                class="p-2 rounded-lg transition-all" title="Lista">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ── BUSCA ──────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-[var(--tu-border)] bg-white shadow-sm transition-all focus-within:border-[var(--tu-primary)] focus-within:ring-2 focus-within:ring-blue-100">
        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="search" x-model="searchQuery"
            placeholder="Buscar por título, descrição ou tag..."
            class="flex-1 bg-transparent text-[var(--tu-text)] placeholder-gray-400 text-sm outline-none border-none">
    </div>

    {{-- ── FILTROS POR TIPO ────────────────────────────────────── --}}
    <div class="flex gap-2 flex-wrap">
        <template x-for="(label, key) in { todos: 'Todos', manual: 'Manuais', ebook: 'E-books', apresentacao: 'Apresentações', video: 'Vídeos' }" :key="key">
            <button @click="activeType = key"
                :class="activeType === key
                    ? 'bg-[var(--tu-primary)] text-white shadow-md shadow-blue-200'
                    : 'bg-white text-[var(--tu-text-secondary)] border border-[var(--tu-border)] hover:border-[var(--tu-primary)] hover:text-[var(--tu-primary)]'"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-all">
                <span x-text="label"></span>
                <span class="text-xs font-bold px-1.5 py-0.5 rounded-full"
                    :class="activeType === key ? 'bg-white/25 text-white' : 'bg-gray-100 text-gray-500'"
                    x-text="typeCount(key)"></span>
            </button>
        </template>
    </div>

    {{-- ── NOVIDADES (só sem filtro ativo) ────────────────────── --}}
    <template x-if="activeType === 'todos' && !searchQuery.trim()">
        <div class="tu-card p-5">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-base">✨</span>
                <h2 class="text-sm font-semibold text-[var(--tu-text)]">Adicionados recentemente</h2>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700"
                    x-text="materials.filter(m => m.isNew).length + ' novos'"></span>
            </div>
            <div class="flex gap-3 overflow-x-auto pb-1">
                <template x-for="mat in materials.filter(m => m.isNew)" :key="'new-' + mat.id">
                    <div class="flex-shrink-0 flex items-center gap-3 bg-[var(--tu-bg)] border border-[var(--tu-border)] rounded-xl px-4 py-3 cursor-pointer hover:border-[var(--tu-primary)] hover:shadow-sm transition-all min-w-[200px]">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" :style="`background: ${mat.accent}20`">
                            <svg style="width:18px;height:18px;" :style="`color: ${mat.accent}`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="typeIcons[mat.type]"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-[var(--tu-text)] truncate max-w-[150px]" x-text="mat.title"></p>
                            <p class="text-[11px] text-[var(--tu-text-muted)] mt-0.5" x-text="mat.addedDate"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- ── CONTADOR DE RESULTADOS ──────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-[var(--tu-text-secondary)]">
            Exibindo <span class="font-semibold text-[var(--tu-text)]" x-text="filteredMaterials.length"></span> materiais
            <template x-if="searchQuery.trim()">
                <span> para "<em class="text-[var(--tu-primary)]" x-text="searchQuery"></em>"</span>
            </template>
        </p>
    </div>

    {{-- ── ESTADO VAZIO ────────────────────────────────────────── --}}
    <template x-if="filteredMaterials.length === 0">
        <div class="tu-card text-center py-20">
            <div class="text-5xl mb-4">🔍</div>
            <h3 class="font-semibold text-[var(--tu-text)] mb-2">Nenhum material encontrado</h3>
            <p class="text-sm text-[var(--tu-text-secondary)] mb-6">Tente outros termos ou selecione outra categoria.</p>
            <button @click="activeType = 'todos'; searchQuery = ''"
                class="tu-btn tu-btn-primary">Limpar filtros</button>
        </div>
    </template>

    {{-- ══ MODO GRADE ═══════════════════════════════════════════ --}}
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        <template x-for="mat in filteredMaterials" :key="'g-' + mat.id">
            <div class="tu-card hover-lift flex flex-col overflow-hidden cursor-pointer group">

                {{-- Cabeçalho colorido --}}
                <div class="px-6 pt-6 pb-5 flex items-start gap-4" :style="`background: ${mat.accent}0D`">
                    {{-- Ícone grande --}}
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-sm"
                        :style="`background: white; border: 2px solid ${mat.accent}30`">
                        <svg style="width:26px;height:26px;" :style="`color: ${mat.accent}`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" :d="typeIcons[mat.type]"/>
                        </svg>
                    </div>
                    {{-- Tipo + badge novo --}}
                    <div class="flex-1 pt-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                :style="`background: ${mat.accent}20; color: ${mat.accent}`"
                                x-text="mat.typeLabel"></span>
                            <template x-if="mat.isNew">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 tracking-wide">NOVO</span>
                            </template>
                        </div>
                        <p class="text-[11px] text-[var(--tu-text-muted)] mt-2" x-text="mat.addedDate"></p>
                    </div>
                </div>

                {{-- Corpo --}}
                <div class="px-6 py-4 flex-1 flex flex-col">
                    <h3 class="font-semibold text-[var(--tu-text)] text-sm leading-snug mb-2 line-clamp-2"
                        x-text="mat.title"></h3>
                    <p class="text-xs text-[var(--tu-text-secondary)] leading-relaxed line-clamp-3 flex-1"
                        x-text="mat.description"></p>

                    {{-- Tags --}}
                    <div class="flex flex-wrap gap-2 mt-4">
                        <template x-for="tag in mat.tags" :key="tag">
                            <span class="px-3 py-1 rounded-full text-[11px] font-medium bg-[var(--tu-bg)] border border-[var(--tu-border)] text-[var(--tu-text-secondary)]"
                                x-text="tag"></span>
                        </template>
                    </div>
                </div>

                {{-- Rodapé --}}
                <div class="px-6 py-4 border-t border-[var(--tu-border-light)] flex items-center justify-between bg-[var(--tu-bg)] rounded-b-xl">
                    <div class="flex items-center gap-3 text-xs text-[var(--tu-text-muted)]">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            <span x-text="mat.size"></span>
                        </span>
                        <span>·</span>
                        <span x-text="mat.duration ?? (mat.pages + ' págs')"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <template x-if="mat.downloadable">
                            <button class="p-2 rounded-xl text-gray-400 hover:text-[var(--tu-primary)] hover:bg-white transition-all" title="Baixar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </button>
                        </template>
                        <button class="tu-btn text-xs py-2 px-4 rounded-xl text-white font-semibold transition-all hover:opacity-90 active:scale-95"
                            :style="`background: ${mat.accent}`">
                            <span x-text="mat.type === 'video' ? '▶ Assistir' : 'Acessar'"></span>
                        </button>
                    </div>
                </div>

            </div>
        </template>
    </div>

    {{-- ══ MODO LISTA ═══════════════════════════════════════════ --}}
    <div x-show="viewMode === 'list'" class="flex flex-col gap-3">
        <template x-for="mat in filteredMaterials" :key="'l-' + mat.id">
            <div class="tu-card hover-lift cursor-pointer group flex items-center gap-5 px-5 py-4">

                {{-- Ícone --}}
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                    :style="`background: ${mat.accent}18`">
                    <svg style="width:22px;height:22px;" :style="`color: ${mat.accent}`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" :d="typeIcons[mat.type]"/>
                    </svg>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <h3 class="font-semibold text-sm text-[var(--tu-text)] truncate" x-text="mat.title"></h3>
                        <template x-if="mat.isNew">
                            <span class="flex-shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">NOVO</span>
                        </template>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-[var(--tu-text-muted)] flex-wrap">
                        <span class="font-semibold" :style="`color: ${mat.accent}`" x-text="mat.typeLabel"></span>
                        <span>·</span>
                        <span x-text="mat.size"></span>
                        <span>·</span>
                        <span x-text="mat.duration ?? (mat.pages + ' págs')"></span>
                        <span>·</span>
                        <span x-text="mat.addedDate"></span>
                    </div>
                </div>

                {{-- Tags (desktop) --}}
                <div class="hidden lg:flex gap-2 flex-shrink-0">
                    <template x-for="tag in mat.tags" :key="tag">
                        <span class="px-3 py-1 rounded-full text-[11px] font-medium bg-[var(--tu-bg)] border border-[var(--tu-border)] text-[var(--tu-text-secondary)]"
                            x-text="tag"></span>
                    </template>
                </div>

                {{-- Ações --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    <template x-if="mat.downloadable">
                        <button class="p-2 rounded-xl text-gray-400 hover:text-[var(--tu-primary)] hover:bg-[var(--tu-primary-50)] transition-all" title="Baixar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </button>
                    </template>
                    <button class="tu-btn text-xs py-2 px-4 rounded-xl text-white font-semibold transition-all hover:opacity-90 active:scale-95"
                        :style="`background: ${mat.accent}`">
                        <span x-text="mat.type === 'video' ? '▶ Assistir' : 'Acessar'"></span>
                    </button>
                </div>

            </div>
        </template>
    </div>

    <div class="h-6"></div>
</div>
