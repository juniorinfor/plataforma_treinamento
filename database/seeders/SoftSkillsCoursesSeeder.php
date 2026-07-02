<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonContent;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Seeder;

class SoftSkillsCoursesSeeder extends Seeder
{
    public function run(): void
    {
        if (Course::where('slug', 'comunicacao-assertiva-lideres')->exists()) {
            $this->command->info('SoftSkillsCoursesSeeder: cursos já existem, abortando.');
            return;
        }

        $admin  = User::where('role', 'platform_admin')->firstOrFail();
        $catLid = Category::where('slug', 'lideranca')->firstOrFail();

        $this->comunicacaoAssertiva($admin, $catLid);
        $this->planejamentoProdutividade($admin, $catLid);
        $this->habilidadesSocioemocionais($admin, $catLid);
    }

    // ─────────────────────────────────────────────────────────────
    // CURSO 1 — Comunicação Assertiva para Líderes
    // ─────────────────────────────────────────────────────────────
    private function comunicacaoAssertiva(User $admin, Category $cat): void
    {
        $course = Course::create([
            'company_id'        => null,
            'created_by'        => $admin->id,
            'title'             => 'Comunicação Assertiva para Líderes',
            'slug'              => 'comunicacao-assertiva-lideres',
            'description'       => 'Domine os três estilos de comunicação e aprenda a se expressar com clareza, respeito e confiança — em qualquer conversa, por mais difícil que seja.',
            'short_description' => 'Da comunicação passiva à assertiva: transforme seus diálogos.',
            'category_id'       => $cat->id,
            'difficulty'        => 'intermediate',
            'estimated_hours'   => 4,
            'is_published'      => true,
            'is_platform_course'=> true,
            'published_at'      => now(),
            'xp_reward'         => 120,
        ]);

        // Módulo 1
        $mod1 = Module::create(['course_id' => $course->id, 'title' => 'Os 3 Estilos de Comunicação', 'sort_order' => 1]);

        $l1 = Lesson::create(['module_id' => $mod1->id, 'title' => 'Você sabe como se comunica?', 'type' => 'text', 'sort_order' => 1, 'duration_minutes' => 10, 'xp_reward' => 15]);
        $this->block($l1, 'rich', "# Você sabe como se comunica?\n\nA maioria das pessoas acredita que se comunica bem. Mas quando perguntamos o que elas **realmente querem dizer** em uma conversa difícil — a resposta costuma ser diferente do que disseram.\n\nNessa aula, vamos entender por que isso acontece e qual é o primeiro passo para mudar.", 1);
        $this->block($l1, 'callout', 'Comunicação não é apenas o que você fala — é o que o outro **entende**. E a diferença entre esses dois pode custar relacionamentos, projetos e oportunidades.', 2, ['style' => 'tip', 'title' => 'A distância entre intenção e impacto']);
        $this->block($l1, 'scale', 'Como você avalia sua comunicação no trabalho hoje?', 3, ['minLabel' => 'Muito passiva ou agressiva', 'maxLabel' => 'Totalmente assertiva']);
        $this->block($l1, 'reflection', 'Pense em uma situação recente em que você não disse o que queria dizer. O que te impediu?', 4);

        $l2 = Lesson::create(['module_id' => $mod1->id, 'title' => 'Passivo, Agressivo ou Assertivo?', 'type' => 'text', 'sort_order' => 2, 'duration_minutes' => 15, 'xp_reward' => 20]);
        $this->block($l2, 'rich', "## Os 3 Estilos em Ação\n\nCada pessoa desenvolve um padrão de comunicação ao longo da vida. Nenhum é permanente — todos podem ser aprendidos e transformados. A chave é reconhecer onde você está agora.", 1);
        $this->block($l2, 'comparison', '', 2, [
            'columns' => [
                ['title' => '😶 Passivo', 'color' => '#9CA3AF', 'items' => ['Evita conflitos a qualquer custo', 'Cede mesmo quando não concorda', 'Guarda ressentimentos internamente', 'Diz "tudo bem" quando não está', 'Resultado: frustração acumulada']],
                ['title' => '✅ Assertivo', 'color' => '#10B981', 'items' => ['Expressa opiniões com respeito', 'Ouve o outro genuinamente', 'Diz não sem culpa', 'Negocia soluções satisfatórias', 'Resultado: relacionamentos saudáveis']],
                ['title' => '💢 Agressivo', 'color' => '#EF4444', 'items' => ['Impõe a própria opinião', 'Interrompe e desqualifica', 'Usa tom ameaçador ou irônico', 'Ganha a batalha, perde a guerra', 'Resultado: medo e distância']],
            ],
        ]);
        $this->block($l2, 'flashcards', '', 3, [
            'cards' => [
                ['front' => 'Qual é a principal característica da comunicação passiva?', 'back' => 'Priorizar a aprovação dos outros em detrimento das próprias necessidades e opiniões.'],
                ['front' => 'O que diferencia assertividade de agressividade?', 'back' => 'Assertividade respeita os direitos do outro ao mesmo tempo em que defende os próprios. Agressividade ignora os direitos alheios.'],
                ['front' => 'Por que pessoas agressivas raramente percebem seu estilo?', 'back' => 'Elas confundem assertividade com agressividade, acreditando que "falar a verdade sem rodeios" justifica qualquer tom.'],
                ['front' => 'Qual estilo mais prejudica a liderança a longo prazo?', 'back' => 'O passivo — cria equipes sem clareza, cheias de suposições, onde problemas crescem sem serem nomeados.'],
            ],
        ]);

        $l3 = Lesson::create(['module_id' => $mod1->id, 'title' => 'A linguagem do assertivo', 'type' => 'text', 'sort_order' => 3, 'duration_minutes' => 15, 'xp_reward' => 20]);
        $this->block($l3, 'callout', 'A frase assertiva tem estrutura: **Quando X acontece → Eu sinto Y → Preciso de Z**. Ela foca no comportamento, não no julgamento da pessoa.', 1, ['style' => 'info', 'title' => 'A fórmula assertiva']);
        $this->block($l3, 'accordion', '', 2, [
            'items' => [
                ['title' => 'Técnica 1: Falar em primeira pessoa', 'body' => 'Em vez de "Você sempre atrasa as entregas", diga "Quando os prazos não são cumpridos, fico preocupado com o impacto no time." A diferença é que o segundo abre diálogo; o primeiro fecha.'],
                ['title' => 'Técnica 2: O Disco Arranhado', 'body' => 'Repita seu ponto de vista calma e firmemente, sem se deixar desviar por argumentos irrelevantes. Útil quando alguém tenta pressionar ou manipular a conversa.'],
                ['title' => 'Técnica 3: Banco de Névoa', 'body' => 'Concorde com a parte verdadeira da crítica sem ceder no ponto central. Ex: "Você tem razão, poderia ter comunicado isso antes. E ainda assim preciso manter minha decisão."'],
                ['title' => 'Técnica 4: Assertividade Empática', 'body' => 'Reconheça o sentimento do outro antes de expressar o seu. "Entendo que você está pressionado. E preciso que esse prazo seja respeitado."'],
            ],
        ]);
        $this->block($l3, 'reflection', 'Escolha uma técnica acima e escreva como você poderia usá-la em uma situação real que está enfrentando agora.', 3);

        // Módulo 2
        $mod2 = Module::create(['course_id' => $course->id, 'title' => 'Conversas Difíceis', 'sort_order' => 2]);

        $l4 = Lesson::create(['module_id' => $mod2->id, 'title' => 'Feedback assertivo', 'type' => 'text', 'sort_order' => 1, 'duration_minutes' => 15, 'xp_reward' => 20]);
        $this->block($l4, 'rich', "## Dar feedback é um ato de respeito\n\nFeedback não é crítica, não é elogio vazio e não é conselho não solicitado. É informação útil sobre comportamentos observáveis, entregue com intenção de ajudar.", 1);
        $this->block($l4, 'flashcards', '', 2, [
            'cards' => [
                ['front' => 'O que é o modelo SBI de feedback?', 'back' => 'Situação → Comportamento → Impacto. Descreva a situação específica, o comportamento observado e o impacto que ele gerou.'],
                ['front' => 'Como transformar "você é desorganizado" em feedback assertivo?', 'back' => '"Na reunião de segunda (situação), você não trouxe os dados combinados (comportamento), e o time precisou remarcar o cliente (impacto)."'],
                ['front' => 'Quando o feedback positivo deve ser dado?', 'back' => 'Imediatamente após o comportamento desejado — quanto mais próximo do evento, mais eficaz o reforço.'],
                ['front' => 'Qual é o maior erro no feedback corretivo?', 'back' => 'Misturá-lo com avaliação de caráter ("você é irresponsável") em vez de focar no comportamento específico.'],
            ],
        ]);
        $this->block($l4, 'reflection', 'Pense em um feedback que você precisa dar a alguém. Escreva-o usando o modelo SBI: Situação → Comportamento → Impacto.', 3);

        $l5 = Lesson::create(['module_id' => $mod2->id, 'title' => 'Dizer não com respeito', 'type' => 'text', 'sort_order' => 2, 'duration_minutes' => 10, 'xp_reward' => 15]);
        $this->block($l5, 'quote', 'Dizer não é uma frase completa. Não precisa de desculpa, justificativa ou explicação elaborada — precisa apenas de tom respeitoso.', 1, ['author' => 'Anne Lamott']);
        $this->block($l5, 'comparison', '', 2, [
            'columns' => [
                ['title' => '❌ Não assertivo', 'color' => '#EF4444', 'items' => ['"Não sei... talvez..."', '"Pode ser, mas..."', 'Aceita e depois não cumpre', 'Diz sim e ressente']],
                ['title' => '✅ Assertivo', 'color' => '#10B981', 'items' => ['"Não posso assumir isso agora"', '"Prefiro não" (sem explicação elaborada)', 'Oferece alternativa quando possível', 'Mantém o limite com calma']],
            ],
        ]);
        $this->block($l5, 'scale', 'Qual é a sua dificuldade em dizer não hoje?', 3, ['minLabel' => 'Digo não com facilidade', 'maxLabel' => 'Nunca consigo dizer não']);
        $this->block($l5, 'reflection', 'Escreva uma situação em que precisou dizer não e não disse. Como poderia ter sido diferente com a comunicação assertiva?', 4);
    }

    // ─────────────────────────────────────────────────────────────
    // CURSO 2 — Planejamento & Produtividade do Bem
    // ─────────────────────────────────────────────────────────────
    private function planejamentoProdutividade(User $admin, Category $cat): void
    {
        $course = Course::create([
            'company_id'        => null,
            'created_by'        => $admin->id,
            'title'             => 'Planejamento & Produtividade do Bem',
            'slug'              => 'planejamento-produtividade-bem',
            'description'       => 'Descubra como ser mais produtivo sem perder o bem-estar. Aprenda a planejar com propósito, priorizar com inteligência e proteger sua energia ao longo do dia.',
            'short_description' => 'Produtividade com equilíbrio e propósito.',
            'category_id'       => $cat->id,
            'difficulty'        => 'beginner',
            'estimated_hours'   => 3,
            'is_published'      => true,
            'is_platform_course'=> true,
            'published_at'      => now(),
            'xp_reward'         => 100,
        ]);

        // Módulo 1
        $mod1 = Module::create(['course_id' => $course->id, 'title' => 'Os 5 Pilares da Produtividade', 'sort_order' => 1]);

        $l1 = Lesson::create(['module_id' => $mod1->id, 'title' => 'Por que você nunca tem tempo?', 'type' => 'text', 'sort_order' => 1, 'duration_minutes' => 10, 'xp_reward' => 15]);
        $this->block($l1, 'rich', "# Por que você nunca tem tempo?\n\nTodos temos 24 horas. A diferença entre quem parece ter tempo de sobra e quem vive apagando incêndios não está na quantidade de horas — está em como elas são **usadas, defendidas e planejadas**.\n\nProdutividade não é fazer mais. É fazer o que importa, com energia e clareza.", 1);
        $this->block($l1, 'callout', 'O maior inimigo da produtividade não é a preguiça — é a falta de clareza sobre o que realmente importa fazer agora.', 1, ['style' => 'warning', 'title' => 'A armadilha da ocupação']);
        $this->block($l1, 'scale', 'Como você avalia sua produtividade atual?', 3, ['minLabel' => 'Muito reativo, sem planejamento', 'maxLabel' => 'Totalmente planejado e focado']);
        $this->block($l1, 'reflection', 'Liste as 3 principais "ladrões de tempo" da sua semana. O que eles têm em comum?', 4);

        $l2 = Lesson::create(['module_id' => $mod1->id, 'title' => 'Os 5 Pilares em detalhe', 'type' => 'text', 'sort_order' => 2, 'duration_minutes' => 15, 'xp_reward' => 20]);
        $this->block($l2, 'flashcards', '', 1, [
            'cards' => [
                ['front' => '🎯 Pilar 1: Clareza de Propósito', 'back' => 'Saber POR QUÊ você faz o que faz filtra automaticamente o que não merece sua energia. Comece cada semana perguntando: "O que realmente importa esta semana?"'],
                ['front' => '🏆 Pilar 2: Priorização Inteligente', 'back' => 'Nem tudo urgente é importante. Use a Matriz de Eisenhower: Urgente+Importante → faça agora. Importante+Não urgente → agende. Urgente+Não importante → delegue. Nenhum dos dois → elimine.'],
                ['front' => '⚡ Pilar 3: Gestão de Energia', 'back' => 'Respeite seus ciclos de energia. Reserve as horas de pico (geralmente manhã) para tarefas que exigem concentração. Reuniões e tarefas administrativas podem ir para os vales.'],
                ['front' => '🛡️ Pilar 4: Proteção de Foco', 'back' => 'Multitarefa é mito — o cérebro alterna entre tarefas, gastando energia a cada troca. Blocos de tempo dedicados (deep work) multiplicam a produção de qualidade.'],
                ['front' => '🔄 Pilar 5: Revisão e Ajuste', 'back' => 'Produtividade é um sistema vivo. Reserve 15 minutos toda sexta para revisar a semana: o que funcionou, o que travou, o que muda na próxima.'],
            ],
        ]);
        $this->block($l2, 'accordion', '', 2, [
            'items' => [
                ['title' => 'Como aplicar a Matriz de Eisenhower', 'body' => 'Liste todas as suas tarefas pendentes. Para cada uma, pergunte: É urgente? (tem prazo imediato) É importante? (impacta meus objetivos). Coloque em um dos 4 quadrantes e trate conforme a prioridade.'],
                ['title' => 'O que é deep work e como praticar', 'body' => 'Deep work é trabalho cognitivo de alta qualidade sem interrupções. Comece com blocos de 25 minutos (técnica Pomodoro) e aumente gradualmente. Desligue notificações, feche abas desnecessárias e proteja esse tempo como uma reunião importante.'],
                ['title' => 'Como fazer uma revisão semanal eficiente', 'body' => 'Todo final de semana: 1) Revise o que planejou vs. o que fez; 2) Identifique o que travou; 3) Capture tudo que ficou pendente; 4) Defina as 3 prioridades da próxima semana; 5) Bloqueie tempo no calendário para essas prioridades.'],
            ],
        ]);

        // Módulo 2
        $mod2 = Module::create(['course_id' => $course->id, 'title' => 'Ferramentas Práticas', 'sort_order' => 2]);

        $l3 = Lesson::create(['module_id' => $mod2->id, 'title' => 'Planejamento semanal na prática', 'type' => 'text', 'sort_order' => 1, 'duration_minutes' => 15, 'xp_reward' => 20]);
        $this->block($l3, 'rich', "## O ritual do planejamento semanal\n\nPessoas altamente produtivas não improvisam a semana — elas a **arquitetam** antes que ela comece. O planejamento semanal é um ritual de 20-30 minutos que muda completamente a sensação de controle e propósito.", 1);
        $this->block($l3, 'comparison', '', 2, [
            'columns' => [
                ['title' => '😵 Sem planejamento', 'color' => '#EF4444', 'items' => ['Reage ao que aparece', 'Sensação de não ter tempo', 'Prioridades dos outros sempre ganham', 'Semana termina sem sensação de avanço', 'Trabalha mais, entrega menos']],
                ['title' => '🎯 Com planejamento', 'color' => '#10B981', 'items' => ['Age com intenção', 'Clareza sobre o que importa', 'Suas prioridades têm espaço garantido', 'Semana termina com senso de conquista', 'Trabalha melhor, entrega mais']],
            ],
        ]);
        $this->block($l3, 'callout', "**Passo a passo do planejamento semanal:**\n1. Capture tudo que está na cabeça\n2. Defina as 3 entregas mais importantes da semana\n3. Bloqueie tempo para cada uma no calendário\n4. Identifique riscos e possíveis bloqueios\n5. Deixe 20% do tempo livre para imprevistos", 3, ['style' => 'success', 'title' => 'Ritual de 5 passos']);
        $this->block($l3, 'reflection', 'Usando o ritual acima, planeje sua próxima semana agora. Quais são as 3 entregas mais importantes? Quando você vai trabalhar nelas?', 4);

        $l4 = Lesson::create(['module_id' => $mod2->id, 'title' => 'Protegendo sua energia', 'type' => 'text', 'sort_order' => 2, 'duration_minutes' => 10, 'xp_reward' => 15]);
        $this->block($l4, 'quote', 'Você não pode gerir o tempo. Você só pode gerir a si mesmo dentro do tempo que tem.', 1, ['author' => 'Peter Drucker']);
        $this->block($l4, 'accordion', '', 2, [
            'items' => [
                ['title' => '🌅 Rotina matinal: os primeiros 60 minutos', 'body' => 'Evite checar e-mail ou redes sociais antes de completar sua primeira tarefa importante. Os primeiros 60 minutos do dia definem o tom de tudo que vem depois. Comece pelo que mais importa.'],
                ['title' => '📵 Gerenciando interrupções', 'body' => 'Defina "horários de atendimento" para e-mails e mensagens. Comunique sua disponibilidade ao time. Use status "Focado" em ferramentas de comunicação. Interrupções são o maior custo oculto de produtividade.'],
                ['title' => '🔋 Recarregando entre blocos', 'body' => 'Pausas não são preguiça — são parte do sistema. A cada 90 minutos de foco, tire 10-15 minutos para se mover, respirar ou descansar os olhos. Isso mantém a qualidade da concentração ao longo do dia.'],
            ],
        ]);
        $this->block($l4, 'scale', 'Hoje, quanto você consegue proteger seu tempo de foco de interrupções?', 3, ['minLabel' => 'Interrompido constantemente', 'maxLabel' => 'Foco total quando preciso']);
        $this->block($l4, 'reflection', 'Identifique um hábito que drena sua energia durante o dia. O que você pode mudar a partir de amanhã?', 4);
    }

    // ─────────────────────────────────────────────────────────────
    // CURSO 3 — Habilidades Socioemocionais
    // ─────────────────────────────────────────────────────────────
    private function habilidadesSocioemocionais(User $admin, Category $cat): void
    {
        $course = Course::create([
            'company_id'        => null,
            'created_by'        => $admin->id,
            'title'             => 'Habilidades Socioemocionais',
            'slug'              => 'habilidades-socioemocionais',
            'description'       => 'Desenvolva inteligência emocional, empatia e habilidades de relacionamento para construir conexões mais saudáveis no trabalho e na vida.',
            'short_description' => 'Inteligência emocional aplicada ao ambiente de trabalho.',
            'category_id'       => $cat->id,
            'difficulty'        => 'beginner',
            'estimated_hours'   => 3,
            'is_published'      => true,
            'is_platform_course'=> true,
            'published_at'      => now(),
            'xp_reward'         => 100,
        ]);

        // Módulo 1
        $mod1 = Module::create(['course_id' => $course->id, 'title' => 'Inteligência Emocional', 'sort_order' => 1]);

        $l1 = Lesson::create(['module_id' => $mod1->id, 'title' => 'O que é inteligência emocional?', 'type' => 'text', 'sort_order' => 1, 'duration_minutes' => 10, 'xp_reward' => 15]);
        $this->block($l1, 'rich', "# Inteligência Emocional\n\nA inteligência emocional (IE) é a capacidade de **reconhecer, entender e gerenciar** as próprias emoções e as dos outros. Pesquisas mostram que ela é responsável por até 58% do desempenho profissional — mais do que o QI em muitas situações.\n\nA boa notícia: ao contrário do QI, a IE pode ser desenvolvida ao longo da vida.", 1);
        $this->block($l1, 'callout', 'Segundo Daniel Goleman, 90% das pessoas que apresentam alto desempenho têm alta inteligência emocional. Apenas 20% do sucesso profissional é atribuído ao QI.', 2, ['style' => 'info', 'title' => 'Dado que importa']);
        $this->block($l1, 'flashcards', '', 3, [
            'cards' => [
                ['front' => '🔍 Autoconsciência', 'back' => 'Reconhecer suas próprias emoções no momento em que elas ocorrem. É a base da IE — você não pode gerenciar o que não reconhece.'],
                ['front' => '🎛️ Autorregulação', 'back' => 'Gerenciar emoções de forma saudável: pausar antes de reagir, não agir por impulso, manter a calma sob pressão.'],
                ['front' => '🔥 Motivação', 'back' => 'Comprometer-se com objetivos além de recompensas externas. Pessoas com alta IE são movidas por propósito, não apenas por salário ou status.'],
                ['front' => '🤝 Empatia', 'back' => 'Compreender as emoções dos outros, mesmo sem concordar com elas. Empatia não é concordância — é conexão.'],
                ['front' => '🌐 Habilidades Sociais', 'back' => 'Gerenciar relacionamentos, influenciar positivamente, resolver conflitos e trabalhar bem em grupo.'],
            ],
        ]);

        $l2 = Lesson::create(['module_id' => $mod1->id, 'title' => 'Autoconhecimento emocional', 'type' => 'text', 'sort_order' => 2, 'duration_minutes' => 15, 'xp_reward' => 20]);
        $this->block($l2, 'rich', "## Conhecendo seus gatilhos\n\nGatilhos emocionais são situações, palavras ou comportamentos que disparam emoções intensas em nós. Reconhecê-los é o primeiro passo para não sermos controlados por eles.", 1);
        $this->block($l2, 'accordion', '', 2, [
            'items' => [
                ['title' => 'Como identificar seus gatilhos', 'body' => 'Observe quando você reage de forma desproporcional a uma situação. Pergunte: "Essa reação é proporcional ao que aconteceu?" Se não for, há um gatilho ativo. Anote o padrão: o que aconteceu, o que sentiu, o que fez.'],
                ['title' => 'A pausa de 6 segundos', 'body' => 'Quando sentir uma emoção intensa, pause por 6 segundos antes de reagir. Esse tempo permite que a amígdala (centro de reação emocional) receba input do córtex pré-frontal (raciocínio). É biologicamente suficiente para mudar a qualidade da resposta.'],
                ['title' => 'Nomear para dominar', 'body' => 'Pesquisas mostram que simplesmente nomear uma emoção ("Estou sentindo ansiedade agora") reduz a intensidade da experiência emocional. Quanto mais vocabulário emocional você tem, mais controle você exerce.'],
                ['title' => 'Diário emocional', 'body' => 'Reserve 5 minutos ao final do dia para registrar: Quais emoções senti? O que as causou? Como reagi? O que faria diferente? Após 2 semanas, você verá padrões claros.'],
            ],
        ]);
        $this->block($l2, 'scale', 'Com que frequência você identifica suas emoções no momento em que elas surgem?', 3, ['minLabel' => 'Raramente, percebo depois', 'maxLabel' => 'Sempre, em tempo real']);
        $this->block($l2, 'reflection', 'Qual é o seu principal gatilho emocional no trabalho? Descreva uma situação recente em que ele foi ativado e como você respondeu.', 4);

        // Módulo 2
        $mod2 = Module::create(['course_id' => $course->id, 'title' => 'Empatia e Relacionamentos', 'sort_order' => 2]);

        $l3 = Lesson::create(['module_id' => $mod2->id, 'title' => 'Empatia em ação', 'type' => 'text', 'sort_order' => 1, 'duration_minutes' => 15, 'xp_reward' => 20]);
        $this->block($l3, 'quote', 'Empatia é ver com os olhos do outro, ouvir com os ouvidos do outro e sentir com o coração do outro.', 1, ['author' => 'Alfred Adler']);
        $this->block($l3, 'rich', "## Os 3 tipos de empatia\n\nNem toda empatia é igual. Entender os tipos ajuda a usar a mais adequada em cada situação.", 2);
        $this->block($l3, 'comparison', '', 3, [
            'columns' => [
                ['title' => '🧠 Cognitiva', 'color' => '#3B82F6', 'items' => ['Entender o ponto de vista do outro', 'Ver a situação pela perspectiva dele', 'Útil em negociações', 'Base da comunicação assertiva']],
                ['title' => '💙 Afetiva', 'color' => '#8B5CF6', 'items' => ['Sentir o que o outro sente', 'Conexão emocional genuína', 'Útil em suporte e acolhimento', 'Requer gestão para não se perder']],
                ['title' => '🤲 Compassiva', 'color' => '#10B981', 'items' => ['Entender + sentir + agir', 'Motivação para ajudar', 'A forma mais completa', 'Base da liderança servidora']],
            ],
        ]);
        $this->block($l3, 'callout', "Empatia **não é concordância**. Você pode entender e validar o sentimento do outro sem concordar com a posição ou comportamento dele.", 4, ['style' => 'warning', 'title' => 'Atenção']);
        $this->block($l3, 'reflection', 'Pense em alguém com quem você tem dificuldade de se relacionar no trabalho. Tente descrever a situação do ponto de vista DELE — o que ele pode estar sentindo ou pensando?', 5);

        $l4 = Lesson::create(['module_id' => $mod2->id, 'title' => 'Regulação emocional no trabalho', 'type' => 'text', 'sort_order' => 2, 'duration_minutes' => 10, 'xp_reward' => 15]);
        $this->block($l4, 'rich', "## Regulação ≠ Supressão\n\nMuitas pessoas confundem regular emoções com **não sentir** ou **esconder** o que sentem. São coisas diferentes: supressão aumenta o estresse; regulação processa e direciona a emoção de forma saudável.", 1);
        $this->block($l4, 'accordion', '', 2, [
            'items' => [
                ['title' => '🫁 Respiração como ferramenta', 'body' => 'A respiração 4-7-8 ativa o sistema nervoso parassimpático (relaxamento): inspire por 4 segundos, segure por 7, expire por 8. Use quando sentir ansiedade ou raiva crescendo.'],
                ['title' => '🏃 Movimento como válvula', 'body' => 'Emoções intensas produzem hormônios de estresse. Movimento físico — mesmo uma caminhada de 5 minutos — metaboliza esses hormônios e restaura o equilíbrio emocional.'],
                ['title' => '💬 Externalização segura', 'body' => 'Escrever sobre o que você sente — mesmo sem nunca mostrar para ninguém — processa a emoção de forma eficaz. É como liberar pressão de uma panela antes de abrir a tampa.'],
                ['title' => '🔄 Reencadramento cognitivo', 'body' => 'Pergunte: "Isso ainda vai importar em 5 anos?" ou "Como eu poderia ver isso de forma diferente?" O cérebro não consegue sentir e raciocinar ao mesmo tempo com a mesma intensidade.'],
            ],
        ]);
        $this->block($l4, 'scale', 'Quando você está sob pressão, o quanto consegue manter a calma e agir racionalmente?', 3, ['minLabel' => 'Perco o controle facilmente', 'maxLabel' => 'Mantenho a calma mesmo sob pressão']);
        $this->block($l4, 'reflection', 'Qual estratégia de regulação emocional você vai experimentar esta semana? Em qual situação específica você vai aplicá-la?', 4);
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────
    private function block(Lesson $lesson, string $type, string $content, int $order, ?array $settings = null): void
    {
        LessonContent::create([
            'lesson_id' => $lesson->id,
            'type'      => $type,
            'content'   => $content,
            'sort_order'=> $order,
            'settings'  => $settings,
        ]);
    }
}
