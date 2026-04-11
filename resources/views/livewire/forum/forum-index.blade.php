<div
    x-data="{
        activeCategory: 'todos',
        showNewTopicModal: false,
        showThreadModal: false,
        selectedTopic: null,
        quickReplyText: '',
        topics: [
            {
                id: 1,
                title: 'Como acessar o certificado após concluir o curso?',
                category: 'cursos',
                categoryLabel: 'Cursos',
                author: 'Ana Beatriz',
                authorInitials: 'AB',
                authorColor: '#6366f1',
                date: 'Há 2 horas',
                replies: 14,
                views: 238,
                hot: true,
                pinned: true,
                excerpt: 'Finalizei o curso de Integração com Sistemas mas não consigo encontrar o certificado para baixar. Alguém pode me ajudar?'
            },
            {
                id: 2,
                title: 'Sugestão: adicionar trilha de aprendizagem para líderes de equipe',
                category: 'sugestoes',
                categoryLabel: 'Sugestões',
                author: 'Carlos Menezes',
                authorInitials: 'CM',
                authorColor: '#10b981',
                date: 'Há 5 horas',
                replies: 7,
                views: 102,
                hot: false,
                pinned: false,
                excerpt: 'Seria muito útil ter uma trilha específica para desenvolvimento de líderes, focada em gestão de pessoas e tomada de decisão.'
            },
            {
                id: 3,
                title: 'Dúvida sobre avaliação do módulo de Segurança do Trabalho',
                category: 'duvidas',
                categoryLabel: 'Dúvidas',
                author: 'Fernanda Lopes',
                authorInitials: 'FL',
                authorColor: '#f59e0b',
                date: 'Há 1 dia',
                replies: 22,
                views: 415,
                hot: true,
                pinned: false,
                excerpt: 'Realizei a avaliação três vezes e não estou conseguindo passar. Qual é a pontuação mínima exigida para aprovação?'
            },
            {
                id: 4,
                title: 'Apresentação: novo colaborador do time de TI',
                category: 'geral',
                categoryLabel: 'Geral',
                author: 'Roberto Silva',
                authorInitials: 'RS',
                authorColor: '#ec4899',
                date: 'Há 2 dias',
                replies: 31,
                views: 560,
                hot: true,
                pinned: false,
                excerpt: 'Olá a todos! Me chamo Roberto e sou o novo analista de infraestrutura do time de TI. Animado para aprender com vocês!'
            },
            {
                id: 5,
                title: 'O curso de Excel Avançado vale muito a pena!',
                category: 'cursos',
                categoryLabel: 'Cursos',
                author: 'Juliana Pereira',
                authorInitials: 'JP',
                authorColor: '#8b5cf6',
                date: 'Há 3 dias',
                replies: 18,
                views: 307,
                hot: false,
                pinned: false,
                excerpt: 'Queria compartilhar minha experiência com o curso de Excel Avançado. Aprendi muito e já estou aplicando no trabalho!'
            },
            {
                id: 6,
                title: 'Sugestão: gamificação com rankings entre departamentos',
                category: 'sugestoes',
                categoryLabel: 'Sugestões',
                author: 'Marcos Andrade',
                authorInitials: 'MA',
                authorColor: '#14b8a6',
                date: 'Há 4 dias',
                replies: 9,
                views: 189,
                hot: false,
                pinned: false,
                excerpt: 'E se houvesse uma competição saudável entre os departamentos para ver qual completa mais cursos no mês?'
            },
            {
                id: 7,
                title: 'Problemas ao assistir vídeos no celular',
                category: 'duvidas',
                categoryLabel: 'Dúvidas',
                author: 'Patricia Santos',
                authorInitials: 'PS',
                authorColor: '#f97316',
                date: 'Há 5 dias',
                replies: 5,
                views: 88,
                hot: false,
                pinned: false,
                excerpt: 'Ao tentar assistir as aulas pelo celular, o vídeo trava constantemente. Alguém mais está tendo esse problema?'
            },
            {
                id: 8,
                title: 'Dica: como organizar seu plano de estudos semanal',
                category: 'geral',
                categoryLabel: 'Geral',
                author: 'Lucas Ferreira',
                authorInitials: 'LF',
                authorColor: '#06b6d4',
                date: 'Há 1 semana',
                replies: 42,
                views: 891,
                hot: true,
                pinned: false,
                excerpt: 'Criei um método simples para organizar meu plano de estudos e aumentei minha produtividade em 60%. Vou compartilhar aqui!'
            }
        ],
        contributors: [
            { name: 'Lucas Ferreira', initials: 'LF', color: '#06b6d4', posts: 89, badge: '🏆' },
            { name: 'Ana Beatriz', initials: 'AB', color: '#6366f1', posts: 74, badge: '🥈' },
            { name: 'Fernanda Lopes', initials: 'FL', color: '#f59e0b', posts: 61, badge: '🥉' },
            { name: 'Roberto Silva', initials: 'RS', color: '#ec4899', posts: 53, badge: '' },
            { name: 'Juliana Pereira', initials: 'JP', color: '#8b5cf6', posts: 47, badge: '' }
        ],
        categories: [
            { key: 'todos', label: 'Todos', count: 8, icon: '📋' },
            { key: 'cursos', label: 'Cursos', count: 2, icon: '🎓' },
            { key: 'duvidas', label: 'Dúvidas', count: 2, icon: '❓' },
            { key: 'sugestoes', label: 'Sugestões', count: 2, icon: '💡' },
            { key: 'geral', label: 'Geral', count: 2, icon: '💬' }
        ],
        get filteredTopics() {
            if (this.activeCategory === 'todos') return this.topics;
            return this.topics.filter(t => t.category === this.activeCategory);
        },
        openThread(topic) {
            this.selectedTopic = topic;
            this.showThreadModal = true;
            this.quickReplyText = '';
        },
        categoryColor(cat) {
            const map = {
                cursos: 'bg-indigo-100 text-indigo-700',
                duvidas: 'bg-amber-100 text-amber-700',
                sugestoes: 'bg-emerald-100 text-emerald-700',
                geral: 'bg-sky-100 text-sky-700'
            };
            return map[cat] || 'bg-gray-100 text-gray-700';
        }
    }"
    class="animate-fade-in"
>

    {{-- ===== CABEÇALHO ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 animate-slide-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <span class="text-3xl">💬</span>
                Fórum da Comunidade
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">
                Tire dúvidas, compartilhe ideias e conecte-se com colegas
            </p>
        </div>
        <button
            @click="showNewTopicModal = true"
            class="tu-btn tu-btn-primary flex items-center gap-2 self-start sm:self-auto"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Tópico
        </button>
    </div>

    {{-- ===== LAYOUT PRINCIPAL ===== --}}
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- COLUNA ESQUERDA: Filtros + Lista --}}
        <div class="flex-1 min-w-0">

            {{-- Filtros por categoria --}}
            <div class="flex flex-wrap gap-2 mb-5">
                <template x-for="cat in categories" :key="cat.key">
                    <button
                        @click="activeCategory = cat.key"
                        :class="activeCategory === cat.key
                            ? 'text-white shadow-md'
                            : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-indigo-400 hover:text-indigo-600'"
                        :style="activeCategory === cat.key ? 'background: var(--tu-primary)' : ''"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200"
                    >
                        <span x-text="cat.icon"></span>
                        <span x-text="cat.label"></span>
                        <span
                            :class="activeCategory === cat.key ? 'bg-white/25 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500'"
                            class="ml-0.5 px-1.5 py-0.5 rounded-full text-xs font-semibold"
                            x-text="cat.count"
                        ></span>
                    </button>
                </template>
            </div>

            {{-- Lista de tópicos --}}
            <div class="space-y-3">
                <template x-if="filteredTopics.length === 0">
                    <div class="tu-card text-center py-12">
                        <div class="text-5xl mb-3">🔍</div>
                        <p class="text-gray-500 dark:text-gray-400">Nenhum tópico nesta categoria ainda.</p>
                    </div>
                </template>

                <template x-for="topic in filteredTopics" :key="topic.id">
                    <div
                        class="tu-card p-5 hover:shadow-md transition-all duration-200 cursor-pointer group"
                        :class="topic.pinned ? 'border-l-4' : ''"
                        :style="topic.pinned ? 'border-left-color: var(--tu-primary)' : ''"
                        @click="openThread(topic)"
                    >
                        <div class="flex items-start gap-5">

                            {{-- Avatar --}}
                            <div
                                class="w-14 h-14 rounded-full flex items-center justify-center text-white font-bold text-base flex-shrink-0 shadow-sm"
                                :style="`background: ${topic.authorColor}`"
                                x-text="topic.authorInitials"
                            ></div>

                            {{-- Conteúdo --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    {{-- Pinned --}}
                                    <template x-if="topic.pinned">
                                        <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.293 2.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414L9.293 17.121a1 1 0 01-1.414-1.414L13.172 11H3a1 1 0 110-2h10.172L7.879 3.707a1 1 0 010-1.414z"/>
                                            </svg>
                                            Fixado
                                        </span>
                                    </template>

                                    {{-- Badge categoria --}}
                                    <span
                                        class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                        :class="categoryColor(topic.category)"
                                        x-text="topic.categoryLabel"
                                    ></span>

                                    {{-- Hot indicator --}}
                                    <template x-if="topic.hot">
                                        <span class="text-xs font-semibold text-orange-500 flex items-center gap-1">
                                            🔥 Em alta
                                        </span>
                                    </template>
                                </div>

                                <h3
                                    class="font-semibold text-gray-900 dark:text-white text-base leading-snug group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate"
                                    x-text="topic.title"
                                ></h3>

                                <p
                                    class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-1"
                                    x-text="topic.excerpt"
                                ></p>

                                <div class="flex flex-wrap items-center gap-4 mt-2 text-xs text-gray-400 dark:text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span x-text="topic.author"></span>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span x-text="topic.date"></span>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        💬 <span x-text="topic.replies + ' respostas'"></span>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        👁️ <span x-text="topic.views + ' views'"></span>
                                    </span>
                                </div>
                            </div>

                            {{-- Seta --}}
                            <div class="hidden sm:flex items-center self-center text-gray-300 group-hover:text-indigo-400 transition-colors flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- COLUNA DIREITA: Sidebar --}}
        <div class="lg:w-72 flex-shrink-0 space-y-5">

            {{-- Top Contribuidores --}}
            <div class="tu-card p-5">
                <h2 class="font-bold text-gray-900 mb-5 flex items-center gap-2">
                    <span class="text-xl">🏆</span>
                    Top Contribuidores
                </h2>
                <div class="space-y-4">
                    <template x-for="(c, idx) in contributors" :key="c.name">
                        <div class="flex items-center gap-4">
                            <div class="relative flex-shrink-0">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-sm"
                                    :style="`background: ${c.color}`"
                                    x-text="c.initials"
                                ></div>
                                <template x-if="c.badge">
                                    <span class="absolute -top-1 -right-1 text-sm leading-none" x-text="c.badge"></span>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate" x-text="c.name"></p>
                                <p class="text-xs text-gray-400 mt-0.5" x-text="c.posts + ' posts'"></p>
                            </div>
                            <div class="flex-shrink-0">
                                <span
                                    class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white"
                                    :style="'background: var(--tu-primary)'"
                                    x-text="'#' + (idx + 1)"
                                ></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Categorias --}}
            <div class="tu-card p-5">
                <h2 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="text-xl">📁</span>
                    Categorias
                </h2>
                <div class="space-y-1">
                    <template x-for="cat in categories.filter(c => c.key !== 'todos')" :key="cat.key">
                        <button
                            @click="activeCategory = cat.key"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-colors hover:bg-gray-50"
                            :class="activeCategory === cat.key ? 'text-indigo-600 font-semibold bg-indigo-50' : 'text-gray-600'"
                        >
                            <span class="flex items-center gap-3">
                                <span x-text="cat.icon"></span>
                                <span x-text="cat.label"></span>
                            </span>
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500"
                                x-text="cat.count"
                            ></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- CTA Participar --}}
            <div class="rounded-2xl p-5" style="background: linear-gradient(135deg, var(--tu-primary) 0%, #818cf8 100%);">
                <div class="text-white">
                    <div class="text-3xl mb-3">💡</div>
                    <h3 class="font-bold text-base mb-2">Participe do Fórum!</h3>
                    <p class="text-sm text-white/80 leading-relaxed mb-4">
                        Compartilhe conhecimento, tire dúvidas e ajude seus colegas a crescerem juntos.
                    </p>
                    <button
                        @click="showNewTopicModal = true"
                        class="w-full bg-white/20 hover:bg-white/30 text-white text-sm font-semibold py-2.5 px-4 rounded-xl transition-colors"
                    >
                        Criar meu primeiro tópico
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MODAL: VER TÓPICO / THREAD ===== --}}
    <div
        x-show="showThreadModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);"
        @click.self="showThreadModal = false"
    >
        <div
            x-show="showThreadModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] overflow-hidden flex flex-col"
        >
            {{-- Header do modal --}}
            <div class="flex items-start justify-between p-6 border-b border-gray-100 dark:border-gray-700 flex-shrink-0">
                <div class="flex-1 min-w-0 pr-4" x-show="selectedTopic">
                    <div class="flex flex-wrap gap-2 mb-2" x-show="selectedTopic">
                        <span
                            class="px-2 py-0.5 rounded-full text-xs font-semibold"
                            :class="selectedTopic ? categoryColor(selectedTopic.category) : ''"
                            x-text="selectedTopic ? selectedTopic.categoryLabel : ''"
                        ></span>
                        <template x-if="selectedTopic && selectedTopic.hot">
                            <span class="text-xs font-semibold text-orange-500">🔥 Em alta</span>
                        </template>
                    </div>
                    <h2
                        class="text-lg font-bold text-gray-900 dark:text-white leading-snug"
                        x-text="selectedTopic ? selectedTopic.title : ''"
                    ></h2>
                    <p class="text-xs text-gray-400 mt-1" x-show="selectedTopic">
                        Por <span class="font-medium" x-text="selectedTopic ? selectedTopic.author : ''"></span>
                        &bull;
                        <span x-text="selectedTopic ? selectedTopic.date : ''"></span>
                    </p>
                </div>
                <button
                    @click="showThreadModal = false"
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Corpo do modal (scrollável) --}}
            <div class="overflow-y-auto flex-1 p-6 space-y-5">

                {{-- Post original --}}
                <div class="flex gap-4" x-show="selectedTopic">
                    <div
                        class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                        :style="selectedTopic ? `background: ${selectedTopic.authorColor}` : ''"
                        x-text="selectedTopic ? selectedTopic.authorInitials : ''"
                    ></div>
                    <div class="flex-1">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed" x-text="selectedTopic ? selectedTopic.excerpt : ''"></p>
                        </div>
                        <div class="flex gap-4 mt-2 text-xs text-gray-400">
                            <button class="hover:text-indigo-600 transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                </svg>
                                Curtir
                            </button>
                            <button class="hover:text-indigo-600 transition-colors">Responder</button>
                        </div>
                    </div>
                </div>

                {{-- Respostas mock --}}
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0" style="background: #10b981;">MR</div>
                        <div class="flex-1">
                            <div class="bg-white dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700 rounded-xl p-4">
                                <p class="text-xs font-semibold text-gray-900 dark:text-white mb-1">Maria Rodrigues <span class="font-normal text-gray-400 ml-1">Há 1 hora</span></p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">Ótima dúvida! Já passei por isso também. Você precisa ir em <strong>Meus Cursos &rarr; Concluídos</strong> e clicar no botão "Emitir Certificado". Às vezes demora alguns minutos para processar.</p>
                            </div>
                            <div class="flex gap-4 mt-2 text-xs text-gray-400">
                                <button class="hover:text-indigo-600 transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                    </svg>
                                    5 curtidas
                                </button>
                                <button class="hover:text-indigo-600 transition-colors">Responder</button>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0" style="background: #f59e0b;">TP</div>
                        <div class="flex-1">
                            <div class="bg-white dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700 rounded-xl p-4">
                                <p class="text-xs font-semibold text-gray-900 dark:text-white mb-1">Thiago Porto <span class="font-normal text-gray-400 ml-1">Há 45 minutos</span></p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">Complementando o que a Maria disse: se ainda não aparecer, tente limpar o cache do navegador. Me ajudou quando tive o mesmo problema!</p>
                            </div>
                            <div class="flex gap-4 mt-2 text-xs text-gray-400">
                                <button class="hover:text-indigo-600 transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                    </svg>
                                    2 curtidas
                                </button>
                                <button class="hover:text-indigo-600 transition-colors">Responder</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resposta rápida --}}
            <div class="border-t border-gray-100 dark:border-gray-700 p-4 flex-shrink-0">
                <div class="flex gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-xs flex-shrink-0" style="background: var(--tu-primary);">EU</div>
                    <div class="flex-1">
                        <textarea
                            x-model="quickReplyText"
                            rows="2"
                            placeholder="Escreva sua resposta..."
                            class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-3 py-2 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 resize-none"
                            style="--tw-ring-color: var(--tu-primary);"
                        ></textarea>
                        <div class="flex justify-end mt-2">
                            <button
                                class="tu-btn tu-btn-primary text-sm"
                                :disabled="!quickReplyText.trim()"
                                :class="!quickReplyText.trim() ? 'opacity-50 cursor-not-allowed' : ''"
                            >
                                Responder
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MODAL: NOVO TÓPICO ===== --}}
    <div
        x-show="showNewTopicModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);"
        @click.self="showNewTopicModal = false"
    >
        <div
            x-show="showNewTopicModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden"
        >
            <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Criar Novo Tópico</h2>
                <button @click="showNewTopicModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título do tópico</label>
                    <input
                        type="text"
                        placeholder="Descreva brevemente sua questão..."
                        class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2"
                        style="--tw-ring-color: var(--tu-primary);"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoria</label>
                    <select class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2" style="--tw-ring-color: var(--tu-primary);">
                        <option value="">Selecione uma categoria</option>
                        <option value="cursos">🎓 Cursos</option>
                        <option value="duvidas">❓ Dúvidas</option>
                        <option value="sugestoes">💡 Sugestões</option>
                        <option value="geral">💬 Geral</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição</label>
                    <textarea
                        rows="4"
                        placeholder="Descreva sua questão em detalhes..."
                        class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 resize-none"
                        style="--tw-ring-color: var(--tu-primary);"
                    ></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button @click="showNewTopicModal = false" class="tu-btn flex-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                        Cancelar
                    </button>
                    <button class="tu-btn tu-btn-primary flex-1">
                        Publicar Tópico
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
