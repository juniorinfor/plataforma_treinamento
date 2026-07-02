<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Course;
use App\Models\DiagnosticActionItem;
use App\Models\DiagnosticActionPlan;
use App\Models\DiagnosticAssessment;
use App\Models\DiagnosticResult;

class DiagnosticActionPlanService
{
    /**
     * Templates de ações por código do sub-índice / dimensão.
     * Cada entrada: [title, description, type]
     * Prioridade: score < 70 → 5 itens  |  score >= 70 → 3 itens (celebrar + manter)
     */
    private array $templates = [
        // ── LTI — Liderança e Trabalho em Equipe ─────────────────────
        'LTI' => [
            [
                'title'       => 'Mapeie os perfis comportamentais da sua equipe',
                'description' => 'Realize uma dinâmica de autoconhecimento (ex.: DISC ou MBTI simplificado) com todos os membros. Identifique pontos fortes e lacunas de colaboração.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Realize reuniões semanais de alinhamento (30 min)',
                'description' => 'Implante o ritual de Weekly Check-in: o que foi feito, o que será feito, quais bloqueios existem. Favorece transparência e pertencimento.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Estabeleça um acordo de times (Team Charter)',
                'description' => 'Documente em conjunto as regras de convivência, canais de comunicação e critérios de decisão da equipe. Revisão trimestral.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Capacite líderes em feedback contínuo',
                'description' => 'Treine gestores no modelo SBI (Situação–Comportamento–Impacto) para que o feedback deixe de ser evento anual e passe a ser hábito.',
                'type'        => 'course',
            ],
            [
                'title'       => 'Leitura: "Os 5 Desafios das Equipes" — Patrick Lencioni',
                'description' => 'Uma leitura essencial sobre confiança, conflito saudável e comprometimento coletivo. Indicado para toda a liderança.',
                'type'        => 'reading',
            ],
        ],

        // ── OCI — Orientação ao Cliente e Inovação ───────────────────
        'OCI' => [
            [
                'title'       => 'Implante o ciclo de Voz do Cliente (VoC)',
                'description' => 'Crie um processo sistemático de coleta de feedback (NPS, entrevistas, pesquisas rápidas) e compartilhe os resultados mensalmente com toda a equipe.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Realize uma Sprint de Inovação trimestral',
                'description' => 'Reserve dois dias por trimestre para que equipes multifuncionais proponham melhorias ou novos serviços. Use o formato Design Sprint (Google Ventures).',
                'type'        => 'action',
            ],
            [
                'title'       => 'Mapeie a Jornada do Cliente (Customer Journey Map)',
                'description' => 'Visualize cada ponto de contato do cliente com sua organização, identificando fricções e oportunidades de encantamento.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Capacite a equipe em Design Thinking',
                'description' => 'O Design Thinking é um método centrado no humano para resolver problemas de forma criativa. Ideal para ampliar a orientação ao cliente.',
                'type'        => 'course',
            ],
            [
                'title'       => 'Reflexão: Como sua organização define valor para o cliente?',
                'description' => 'Conduza uma sessão de reflexão estratégica com sua liderança: qual problema real do cliente vocês resolvem melhor do que qualquer concorrente?',
                'type'        => 'reflection',
            ],
        ],

        // ── RBI — Resultados e Benchmarking Interno ──────────────────
        'RBI' => [
            [
                'title'       => 'Defina e implante OKRs para os próximos 90 dias',
                'description' => 'Escolha 2–3 Objetivos estratégicos e 3–4 Resultados-Chave mensuráveis por objetivo. Revise semanalmente o progresso.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Crie um painel de indicadores (Dashboard) visível para todos',
                'description' => 'Selecione 5–7 KPIs críticos do negócio e exiba-os em tempo real. Transparência nos números gera senso de dono.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Faça benchmarking com referências do seu setor',
                'description' => 'Pesquise as 3 melhores práticas de empresas comparáveis ao seu porte e segmento. Documente as lacunas e escolha uma para atacar imediatamente.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Capacite em Gestão por Resultados e Indicadores',
                'description' => 'Um programa estruturado de OKR, KPIs e cultura de dados transformará a forma como sua equipe toma decisões.',
                'type'        => 'course',
            ],
            [
                'title'       => 'Reflexão: Seus resultados hoje financiam o futuro da empresa?',
                'description' => 'Avalie se os resultados atuais são suficientes para sustentar o crescimento planejado e corrija a rota se necessário.',
                'type'        => 'reflection',
            ],
        ],

        // ── SEI — Sustentabilidade e Ética Institucional ─────────────
        'SEI' => [
            [
                'title'       => 'Elabore ou revise o Código de Conduta da empresa',
                'description' => 'Um código claro, construído com participação dos colaboradores, define os limites éticos e a cultura desejada. Treine 100% da equipe.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Implante um canal de denúncias anônimo',
                'description' => 'Disponibilize um canal seguro para que colaboradores reportem condutas irregulares sem medo de retaliação. Essencial para governança.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Mapeie os impactos socioambientais da sua operação',
                'description' => 'Identifique as externalidades positivas e negativas do seu modelo de negócio e construa um roadmap ESG de 12 meses.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Capacite líderes em ESG e Compliance',
                'description' => 'A sustentabilidade deixou de ser diferencial — é requisito. Treine sua liderança nos fundamentos de ESG, compliance e governança.',
                'type'        => 'course',
            ],
            [
                'title'       => 'Reflexão: Sua empresa existirá daqui a 20 anos? Por quê?',
                'description' => 'Questione a sustentabilidade do seu modelo de negócio a longo prazo. Considere aspectos ambientais, sociais, tecnológicos e de governança.',
                'type'        => 'reflection',
            ],
        ],

        // ── LII — Liderança Influente e Inspiradora ──────────────────
        'LII' => [
            [
                'title'       => 'Crie seu Manifesto de Liderança pessoal',
                'description' => 'Defina em uma página: seus valores inegociáveis, seu estilo de liderança, o que você promete à equipe e o que espera dela.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Implante mentoria estruturada para talentos-chave',
                'description' => 'Selecione 2–3 colaboradores de alto potencial e conduza encontros mensais de 60 min de mentoria. Documente evolução e próximos passos.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Comunique a visão com narrativa (Storytelling)',
                'description' => 'Substitua apresentações com bullet points por histórias reais que conectem o propósito da empresa ao cotidiano dos colaboradores.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Capacite-se em Liderança Inspiradora e Comunicação',
                'description' => 'Líderes inspiradores são feitos, não nascidos. Um programa focado em presença executiva, comunicação e influência pode acelerar sua jornada.',
                'type'        => 'course',
            ],
            [
                'title'       => 'Reflexão: Quem você quer ser como líder daqui a 5 anos?',
                'description' => 'Visualize com clareza o tipo de impacto que deseja ter sobre pessoas e organizações. Escreva uma carta para seu eu futuro.',
                'type'        => 'reflection',
            ],
        ],

        // ── EMO — Equilíbrio Emocional (NR-1) ─────────────────────────
        'EMO' => [
            [
                'title'       => 'Implemente pausas estruturadas durante a jornada',
                'description' => 'Estabeleça intervalos breves (5–10 min) a cada 90 minutos de trabalho intenso. Pausas regulares recuperam concentração e regulação emocional.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Ofereça apoio psicológico acessível',
                'description' => 'Disponibilize um canal de apoio psicológico (EAP, convênio ou parceria) e comunique ativamente sua existência para reduzir o estigma de buscar ajuda.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Treine gestores para identificar sinais de sobrecarga emocional',
                'description' => 'Irritabilidade, isolamento e queda de produtividade podem indicar desgaste emocional. Capacite líderes a agir cedo, antes do afastamento.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Capacite a liderança em gestão emocional e escuta ativa',
                'description' => 'Inteligência emocional aplicada à liderança ajuda a criar ambientes psicologicamente mais seguros e reduz o risco de adoecimento da equipe.',
                'type'        => 'course',
            ],
            [
                'title'       => 'Reflexão: o equilíbrio emocional é prioridade real ou responsabilidade individual?',
                'description' => 'Avalie se existem políticas concretas (não apenas discurso) que sustentam o bem-estar emocional dos colaboradores.',
                'type'        => 'reflection',
            ],
        ],

        // ── SPS — Segurança Psicológica (NR-1) ────────────────────────
        'SPS' => [
            [
                'title'       => 'Institua rituais de escuta sem julgamento',
                'description' => 'Reuniões 1:1 regulares onde erros e dúvidas podem ser expostos sem punição. Segurança psicológica se constrói com repetição, não com um discurso único.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Estabeleça um canal de manifestação anônimo',
                'description' => 'Ofereça um meio seguro para colaboradores relatarem preocupações sem medo de retaliação.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Modele vulnerabilidade na liderança',
                'description' => 'Líderes que admitem erros e pedem ajuda publicamente autorizam a equipe a fazer o mesmo.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Capacite líderes em Segurança Psicológica',
                'description' => 'Formação prática baseada no conceito de Amy Edmondson sobre construção de equipes psicologicamente seguras.',
                'type'        => 'course',
            ],
            [
                'title'       => 'Leitura: "A Organização Sem Medo" — Amy Edmondson',
                'description' => 'Obra referência sobre segurança psicológica nas organizações e seu impacto direto em aprendizagem e desempenho.',
                'type'        => 'reading',
            ],
        ],

        // ── MOT — Motivação e Engajamento (NR-1) ──────────────────────
        'MOT' => [
            [
                'title'       => 'Reconheça publicamente contribuições da equipe',
                'description' => 'Reconhecimento verbal ou formal frequente (não apenas em avaliações anuais) sustenta o engajamento contínuo.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Conecte tarefas ao propósito da empresa',
                'description' => 'Explique o "porquê" por trás das atividades, não apenas o "o quê". Colaboradores engajados entendem o impacto do próprio trabalho.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Revise a distribuição de tarefas repetitivas',
                'description' => 'Alterne responsabilidades e ofereça desafios que estimulem crescimento, reduzindo o risco de desmotivação por rotina.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Capacite gestores em liderança motivacional e feedback',
                'description' => 'Gestores preparados para motivar e dar feedback contínuo sustentam engajamento de forma mais duradoura que bonificações pontuais.',
                'type'        => 'course',
            ],
            [
                'title'       => 'Reflexão: o que motiva sua equipe hoje — salário, propósito, reconhecimento ou crescimento?',
                'description' => 'Direcione as ações de retenção com mais precisão a partir dessa resposta.',
                'type'        => 'reflection',
            ],
        ],

        // ── PER — Sentido de Pertencimento (NR-1) ─────────────────────
        'PER' => [
            [
                'title'       => 'Promova rituais de integração contínuos',
                'description' => 'Não restrinja a integração ao onboarding; crie momentos periódicos de conexão entre os times.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Garanta representatividade nas decisões',
                'description' => 'Inclua vozes diversas em decisões que afetam o dia a dia da equipe.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Combata silos entre setores',
                'description' => 'Promova projetos e encontros interdepartamentais para fortalecer a sensação de pertencer ao todo, não apenas à própria área.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Capacite líderes em cultura organizacional inclusiva',
                'description' => 'Cultura de pertencimento não se sustenta apenas em discurso; exige prática de liderança consistente.',
                'type'        => 'course',
            ],
            [
                'title'       => 'Reflexão: seus colaboradores se sentem parte da história da empresa ou apenas executores de tarefas?',
                'description' => 'Avalie se existem espaços reais de participação e reconhecimento do papel de cada pessoa.',
                'type'        => 'reflection',
            ],
        ],

        // ── CDT — Condições de Trabalho (NR-1) ────────────────────────
        'CDT' => [
            [
                'title'       => 'Realize inspeção do ambiente físico e ergonômico',
                'description' => 'Avalie mobiliário, iluminação, ruído e conforto térmico. Condições inadequadas aumentam fadiga e risco de adoecimento.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Garanta recursos adequados para a função',
                'description' => 'Ferramentas, sistemas e equipamentos insuficientes geram frustração e sobrecarga percebida pelo colaborador.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Formalize canal de solicitação de melhorias no ambiente',
                'description' => 'Permita que colaboradores reportem problemas estruturais com resposta e prazo definidos.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Capacite times responsáveis em ergonomia e bem-estar no ambiente de trabalho',
                'description' => 'Formação técnica para quem cuida da infraestrutura reduz reincidência de problemas estruturais.',
                'type'        => 'course',
            ],
            [
                'title'       => 'Reflexão: se você trabalhasse um dia na função de um colaborador operacional, o que mudaria?',
                'description' => 'Coloque-se no lugar de quem executa a atividade para identificar condições que passam despercebidas na gestão.',
                'type'        => 'reflection',
            ],
        ],

        // ── REC — Reconhecimento e Valorização (NR-1) ─────────────────
        'REC' => [
            [
                'title'       => 'Crie um programa formal de reconhecimento',
                'description' => 'Defina critérios claros e uma cadência regular (mensal/trimestral), evitando informalidade que gera percepção de favoritismo.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Treine líderes para dar feedback de valorização específico',
                'description' => 'Reconhecimento genérico ("bom trabalho") tem menos impacto que feedback específico sobre o que foi bem feito e por quê.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Revise trilhas de crescimento e plano de carreira',
                'description' => 'Falta de perspectiva de crescimento é um dos principais motivos de desengajamento e turnover.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Capacite gestores em feedback e reconhecimento',
                'description' => 'Reconhecimento eficaz é uma habilidade de liderança treinável, não apenas um traço de personalidade.',
                'type'        => 'course',
            ],
            [
                'title'       => 'Reflexão: quando foi a última vez que cada membro da equipe recebeu um reconhecimento específico e genuíno?',
                'description' => 'Se a resposta não vier rápido, esse é o primeiro sinal de que o reconhecimento não está sistematizado.',
                'type'        => 'reflection',
            ],
        ],

        // ── SOB — Sobrecarga de Trabalho (NR-1) ───────────────────────
        'SOB' => [
            [
                'title'       => 'Mapeie a carga de trabalho real por colaborador',
                'description' => 'Compare o volume de demandas com a capacidade real de entrega. Sobrecarga sistêmica é um risco psicossocial previsto na NR-1.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Redistribua tarefas e revise prazos irreais',
                'description' => 'Prazos incompatíveis com a carga de trabalho são gatilho direto de estresse crônico e afastamentos.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Documente a sobrecarga no inventário de riscos do PGR',
                'description' => 'Registre esse fator como risco psicossocial formal, com plano de ação e prazos, conforme a Portaria MTE nº 1.419/2024.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Capacite gestores em gestão de tempo e priorização de equipes',
                'description' => 'Priorização mal feita no nível da gestão se traduz em sobrecarga percebida pela equipe operacional.',
                'type'        => 'course',
            ],
            [
                'title'       => 'Reflexão: quais tarefas da equipe poderiam ser eliminadas, automatizadas ou redistribuídas sem perda de qualidade?',
                'description' => 'Reduzir sobrecarga muitas vezes começa por eliminar trabalho de baixo valor, não por contratar mais gente.',
                'type'        => 'reflection',
            ],
        ],

        // ── PRE — Pressão e Estresse (NR-1) ───────────────────────────
        'PRE' => [
            [
                'title'       => 'Revise metas e prazos com a equipe operacional antes de definir',
                'description' => 'Metas definidas sem consulta a quem executa geram pressão desconectada da realidade da operação.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Implemente rotina de descompressão pós-entregas críticas',
                'description' => 'Após períodos de alta pressão, formalize pausas de recuperação para evitar esgotamento acumulado.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Monitore indicadores de afastamento e presenteísmo',
                'description' => 'Aumento de faltas ou queda de produtividade sem explicação aparente pode sinalizar estresse crônico não reportado.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Capacite lideranças em gestão de estresse e prevenção de burnout',
                'description' => 'Líderes que reconhecem os próprios limites são mais capazes de proteger a equipe de sobrecarga emocional.',
                'type'        => 'course',
            ],
            [
                'title'       => 'Leitura: identificar e mitigar riscos psicossociais conforme a NR-1 atualizada',
                'description' => 'Aprofunde-se nas exigências da Portaria MTE nº 1.419/2024 sobre saúde mental e prevenção no ambiente de trabalho.',
                'type'        => 'reading',
            ],
        ],

        // ── COM — Comunicação e Feedback (NR-1) ───────────────────────
        'COM' => [
            [
                'title'       => 'Estabeleça cadência formal de feedback (não apenas anual)',
                'description' => 'Feedback contínuo previne acúmulo de ruídos e mal-entendidos que geram conflitos silenciosos.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Padronize a comunicação de mudanças e decisões',
                'description' => 'Falta de clareza sobre decisões organizacionais gera ansiedade e boatos. Comunique-se de forma transparente e tempestiva.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Treine equipes em comunicação não violenta e assertiva',
                'description' => 'Habilidades de comunicação reduzem conflitos interpessoais e fortalecem a confiança entre colegas e liderança.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Capacite equipes em comunicação assertiva e feedback construtivo',
                'description' => 'Comunicação clara é uma competência treinável que reduz diretamente ruídos e retrabalho.',
                'type'        => 'course',
            ],
            [
                'title'       => 'Reflexão: sua equipe sabe onde buscar informação confiável sobre mudanças na empresa, ou depende de rumores?',
                'description' => 'A ausência de canais oficiais claros é preenchida por especulação, que corrói a confiança organizacional.',
                'type'        => 'reflection',
            ],
        ],
    ];

    /**
     * Mapeamento: código do índice → slugs de categorias de cursos relacionados.
     * Usado para sugerir cursos da plataforma.
     */
    private array $categorySlugs = [
        'LTI' => ['lideranca', 'gestao-de-equipes', 'trabalho-em-equipe', 'comunicacao'],
        'OCI' => ['inovacao', 'customer-success', 'design-thinking', 'empreendedorismo'],
        'RBI' => ['gestao', 'planejamento-estrategico', 'indicadores', 'financeiro'],
        'SEI' => ['esg', 'compliance', 'governanca', 'sustentabilidade'],
        'LII' => ['lideranca', 'coaching', 'comunicacao', 'desenvolvimento-pessoal'],

        // NR-1 — riscos psicossociais e bem-estar
        'SPS' => ['lideranca'],
        'MOT' => ['lideranca'],
        'REC' => ['lideranca'],
        'COM' => ['lideranca', 'compliance'],
    ];

    // ── Ponto de entrada público ──────────────────────────────────────

    /**
     * Gera (ou regenera) o plano de ação para um assessment concluído.
     * Idempotente: se já existe um plano, recria os itens.
     */
    public function generate(DiagnosticAssessment $assessment): DiagnosticActionPlan
    {
        // Cria ou reutiliza o plano
        $plan = DiagnosticActionPlan::firstOrCreate(
            ['diagnostic_assessment_id' => $assessment->id],
            [
                'user_id' => $assessment->user_id,
                'status'  => 'pending',
            ]
        );

        // Remove itens anteriores (re-geração segura)
        $plan->items()->delete();

        $results = $assessment->results()
            ->with(['componentTool', 'dimension'])
            ->get();

        $order = 1;

        foreach ($results as $result) {
            $code  = $result->componentTool?->code ?? $result->dimension?->code ?? null;
            $score = (float) $result->normalized_score;

            $order = $this->createActionItems($plan, $result, $code, $score, $order);
            $order = $this->createCourseItems($plan, $result, $code, $score, $order);
        }

        // Se não há resultados por índice, gera itens genéricos
        if ($results->isEmpty()) {
            $order = $this->createGenericItems($plan, (float) $assessment->global_score, $order);
        }

        $plan->syncProgress();

        return $plan;
    }

    // ── Criação de itens de ação ──────────────────────────────────────

    private function createActionItems(
        DiagnosticActionPlan $plan,
        DiagnosticResult $result,
        ?string $code,
        float $score,
        int $order
    ): int {
        $templates = $this->templatesFor($code);

        if (empty($templates)) {
            return $order;
        }

        // Critérios de saúde: acima de 70 → apenas 3 itens; abaixo → todos os 5
        $limit = $score >= 70 ? 3 : count($templates);

        foreach (array_slice($templates, 0, $limit) as $tpl) {
            if ($tpl['type'] === 'course') {
                continue; // cursos são tratados separadamente
            }

            DiagnosticActionItem::create([
                'diagnostic_action_plan_id' => $plan->id,
                'diagnostic_result_id'      => $result->id,
                'course_id'                 => null,
                'title'                     => $tpl['title'],
                'description'               => $tpl['description'],
                'type'                      => $tpl['type'],
                'status'                    => 'pending',
                'is_auto_generated'         => true,
                'sort_order'                => $order++,
            ]);
        }

        return $order;
    }

    private function createCourseItems(
        DiagnosticActionPlan $plan,
        DiagnosticResult $result,
        ?string $code,
        float $score,
        int $order
    ): int {
        if (!$code) {
            return $order;
        }

        $slugs = $this->categorySlugs[$code] ?? [];

        if (empty($slugs)) {
            return $order;
        }

        // Busca 1–2 cursos publicados nas categorias relacionadas
        $maxCourses = $score < 55 ? 2 : 1;

        $courses = Course::whereHas('category', fn ($q) => $q->whereIn('slug', $slugs))
            ->where('is_published', true)
            ->orderByDesc('is_platform_course')
            ->orderBy('sort_order')
            ->limit($maxCourses)
            ->get();

        foreach ($courses as $course) {
            DiagnosticActionItem::create([
                'diagnostic_action_plan_id' => $plan->id,
                'diagnostic_result_id'      => $result->id,
                'course_id'                 => $course->id,
                'title'                     => 'Treinamento: ' . $course->title,
                'description'               => $course->short_description ?? $course->description,
                'type'                      => 'course',
                'status'                    => 'pending',
                'is_auto_generated'         => true,
                'sort_order'                => $order++,
            ]);
        }

        // Se não encontrou cursos, insere o item de curso do template como ação textual
        $courseTpl = collect($this->templatesFor($code))
            ->firstWhere('type', 'course');

        if ($courses->isEmpty() && $courseTpl) {
            DiagnosticActionItem::create([
                'diagnostic_action_plan_id' => $plan->id,
                'diagnostic_result_id'      => $result->id,
                'course_id'                 => null,
                'title'                     => $courseTpl['title'],
                'description'               => $courseTpl['description'],
                'type'                      => 'course',
                'status'                    => 'pending',
                'is_auto_generated'         => true,
                'sort_order'                => $order++,
            ]);
        }

        return $order;
    }

    private function createGenericItems(
        DiagnosticActionPlan $plan,
        float $score,
        int $order
    ): int {
        $items = [
            [
                'title'       => 'Revise os resultados com sua equipe de liderança',
                'description' => 'Compartilhe o diagnóstico com os gestores e discuta coletivamente os pontos de atenção identificados.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Defina prioridades de melhoria para os próximos 90 dias',
                'description' => 'Escolha no máximo 3 áreas de melhoria e crie ações concretas com responsável, prazo e indicador de sucesso.',
                'type'        => 'action',
            ],
            [
                'title'       => 'Agende um reassessment em 6 meses',
                'description' => 'Repita o diagnóstico após implementar as melhorias para medir a evolução e ajustar a rota.',
                'type'        => 'action',
            ],
        ];

        foreach ($items as $item) {
            DiagnosticActionItem::create([
                'diagnostic_action_plan_id' => $plan->id,
                'diagnostic_result_id'      => null,
                'course_id'                 => null,
                'title'                     => $item['title'],
                'description'               => $item['description'],
                'type'                      => $item['type'],
                'status'                    => 'pending',
                'is_auto_generated'         => true,
                'sort_order'                => $order++,
            ]);
        }

        return $order;
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function templatesFor(?string $code): array
    {
        if (!$code) {
            return [];
        }

        return $this->templates[strtoupper($code)] ?? [];
    }
}
