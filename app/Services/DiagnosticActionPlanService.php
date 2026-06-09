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
