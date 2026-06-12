<?php

namespace Database\Seeders;

use App\Enums\DiagnosticToolType;
use App\Models\DiagnosticDimension;
use App\Models\DiagnosticQuestion;
use App\Models\DiagnosticQuestionOption;
use App\Models\DiagnosticTool;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * AS SCORE® — Diagnóstico de Inteligência Organizacional (versão oficial).
 *
 * Substitui o conteúdo provisório (composto por 5 índices-tool) por um único
 * questionário com 5 índices + IOH (Índice de Inteligência Organizacional
 * Humana, derivado de 13 perguntas marcadas) + pergunta estratégica (eNPS).
 *
 * Escala 1-5 (Discordo Totalmente → Concordo Totalmente).
 *
 * Idempotente/seguro: só executa se o IO ainda for a versão antiga (composta)
 * ou se não existir. Se já houver IO simples (versão nova), não faz nada —
 * evita destruir respostas reais.
 */
class AsScoreSeeder extends Seeder
{
    /** Escala de concordância padrão. */
    private array $likert = [
        ['label' => 'Discordo Totalmente',       'value' => 1],
        ['label' => 'Discordo Parcialmente',     'value' => 2],
        ['label' => 'Nem Concordo Nem Discordo', 'value' => 3],
        ['label' => 'Concordo Parcialmente',     'value' => 4],
        ['label' => 'Concordo Totalmente',       'value' => 5],
    ];

    public function run(): void
    {
        $existing = DiagnosticTool::where('code', 'IO')->first();

        if ($existing && $existing->type === DiagnosticToolType::Simple) {
            $this->command?->warn('AS SCORE® (IO simples) já existe — seeder ignorado.');
            return;
        }

        // Remove a versão antiga (IO composto + tools-índice) e dependências
        // (assessments/respostas/resultados caem por cascade de FK).
        DiagnosticTool::whereIn('code', ['IO', 'LTI', 'OCI', 'RBI', 'SEI', 'LII'])
            ->get()
            ->each
            ->delete();

        $admin = User::where('role', 'platform_admin')->first()
            ?? User::where('role', 'company_admin')->first();

        $tool = DiagnosticTool::create([
            'company_id'        => null,
            'created_by'        => $admin?->id,
            'code'              => 'IO',
            'name'              => 'Diagnóstico de Inteligência Organizacional',
            'slug'              => 'diagnostico-inteligencia-organizacional',
            'short_description' => 'AS SCORE® — fotografia da saúde organizacional pela percepção de quem vive a empresa.',
            'description'       => 'O AS SCORE® mede a Inteligência Organizacional a partir de 5 índices '
                . '(Cultura e Pertencimento, Liderança e Gestão, Bem-estar e Segurança Psicológica, '
                . 'Performance e Produtividade, Comunicação e Integração) e do IOH — Índice de '
                . 'Inteligência Organizacional Humana, indicador proprietário da AD Singular calculado '
                . 'a partir de questões estratégicas distribuídas pelos índices. Transforma a experiência '
                . 'dos colaboradores em dados objetivos para decisão.',
            'type'              => 'simple',
            'input_source'      => 'questionnaire',
            'result_mode'       => 'aggregated',
            'is_confidential'   => true,
            'min_responses'     => 5,
            'requires_review'   => true,
            'icon'              => 'chart-pie',
            'color'             => '#4F46E5',
            'estimated_minutes' => 6,
            'is_published'      => true,
            'is_platform_tool'  => true,
            'sort_order'        => 1,
            'xp_reward'         => 120,
            'settings'          => [
                'methodology' => 'AS SCORE®',
                'scale'       => '1-5',
                'derived'     => ['IOH'],
            ],
        ]);

        // ── Dimensões ─────────────────────────────────────────────────
        $dimensions = [
            'CUL'  => ['name' => 'Cultura e Pertencimento',            'color' => '#6366F1', 'weight' => 1, 'sort' => 1],
            'LID'  => ['name' => 'Liderança e Gestão',                 'color' => '#8B5CF6', 'weight' => 1, 'sort' => 2],
            'BEM'  => ['name' => 'Bem-estar e Segurança Psicológica',  'color' => '#10B981', 'weight' => 1, 'sort' => 3],
            'PER'  => ['name' => 'Performance e Produtividade',        'color' => '#3B82F6', 'weight' => 1, 'sort' => 4],
            'COM'  => ['name' => 'Comunicação e Integração',           'color' => '#F59E0B', 'weight' => 1, 'sort' => 5],
            // Pergunta estratégica (eNPS): aparece como etapa, peso 0 (não pontua índice).
            'ENPS' => ['name' => 'Visão Geral',                        'color' => '#64748B', 'weight' => 0, 'sort' => 6],
            // IOH: dimensão derivada, sem perguntas próprias (calculada no scoring).
            'IOH'  => ['name' => 'IOH — Inteligência Organizacional Humana', 'color' => '#4F46E5', 'weight' => 0, 'sort' => 7],
        ];

        $dims = [];
        foreach ($dimensions as $code => $d) {
            $dims[$code] = DiagnosticDimension::create([
                'diagnostic_tool_id' => $tool->id,
                'code'               => $code,
                'name'               => $d['name'],
                'slug'               => Str::slug($code),
                'color'              => $d['color'],
                'weight'             => $d['weight'],
                'sort_order'         => $d['sort'],
            ]);
        }

        // ── Perguntas (P1-P25) ────────────────────────────────────────
        // [dimensão, enunciado, é IOH?]
        $questions = [
            // Índice 1 — Cultura e Pertencimento
            ['CUL', 'Compreendo claramente como meu trabalho contribui para os objetivos da empresa.', true],
            ['CUL', 'Percebo coerência entre os valores divulgados pela empresa e suas práticas do dia a dia.', true],
            ['CUL', 'Sinto orgulho em fazer parte desta organização.', true],
            ['CUL', 'As pessoas são tratadas com respeito independentemente de cargo ou função.', true],
            ['CUL', 'Sinto que pertenço a um ambiente onde posso contribuir de forma significativa.', true],
            // Índice 2 — Liderança e Gestão
            ['LID', 'Meu líder comunica claramente prioridades e expectativas.', false],
            ['LID', 'Recebo apoio quando enfrento dificuldades no trabalho.', false],
            ['LID', 'Meu líder reconhece adequadamente os esforços e resultados da equipe.', true],
            ['LID', 'Recebo feedbacks que contribuem para meu desenvolvimento profissional.', false],
            ['LID', 'Confio nas decisões tomadas pela minha liderança.', true],
            // Índice 3 — Bem-estar e Segurança Psicológica
            ['BEM', 'Posso expressar opiniões ou sugestões sem receio de consequências negativas.', true],
            ['BEM', 'Quando ocorrem erros, eles são tratados como oportunidades de aprendizado e não apenas de punição.', true],
            ['BEM', 'Percebo preocupação genuína da empresa com o bem-estar das pessoas.', false],
            ['BEM', 'Consigo equilibrar adequadamente as demandas do trabalho com minha energia e disposição.', false],
            ['BEM', 'Considero este um ambiente emocionalmente saudável para trabalhar.', true],
            // Índice 4 — Performance e Produtividade
            ['PER', 'Tenho clareza sobre os resultados esperados do meu trabalho.', false],
            ['PER', 'Possuo os recursos necessários para desempenhar minhas atividades com qualidade.', false],
            ['PER', 'Tenho autonomia adequada para tomar decisões relacionadas ao meu trabalho.', false],
            ['PER', 'Os processos da empresa facilitam a realização das atividades.', false],
            ['PER', 'Sinto que meu trabalho gera valor para a organização.', true],
            // Índice 5 — Comunicação e Integração
            ['COM', 'As informações importantes chegam até mim de forma clara e no momento adequado.', false],
            ['COM', 'Existe colaboração entre equipes e áreas da empresa.', false],
            ['COM', 'Percebo abertura da empresa para ouvir opiniões e sugestões dos colaboradores.', true],
            ['COM', 'Mudanças e decisões relevantes são comunicadas de forma adequada.', false],
            ['COM', 'Sinto que minha opinião pode contribuir para melhorias na organização.', true],
        ];

        $sortByDim = [];
        foreach ($questions as $i => [$dimCode, $content, $isIoh]) {
            $sortByDim[$dimCode] = ($sortByDim[$dimCode] ?? 0) + 1;

            $question = DiagnosticQuestion::create([
                'diagnostic_tool_id'      => $tool->id,
                'diagnostic_dimension_id' => $dims[$dimCode]->id,
                'type'                    => 'scale',
                'content'                 => $content,
                'is_required'             => true,
                'reverse_scored'          => false,
                'weight'                  => 1,
                'sort_order'              => $sortByDim[$dimCode],
                'settings'                => ['scale_min' => 1, 'scale_max' => 5, 'ioh' => $isIoh],
            ]);

            $this->attachLikert($question);
        }

        // ── Pergunta estratégica final (eNPS) ─────────────────────────
        $enps = DiagnosticQuestion::create([
            'diagnostic_tool_id'      => $tool->id,
            'diagnostic_dimension_id' => $dims['ENPS']->id,
            'type'                    => 'scale',
            'content'                 => 'Se pudesse escolher novamente, você continuaria trabalhando nesta empresa?',
            'help_text'               => 'Pergunta estratégica de lealdade (eNPS). Não compõe a nota dos índices.',
            'is_required'             => true,
            'reverse_scored'          => false,
            'weight'                  => 0,
            'sort_order'              => 1,
            'settings'                => ['scale_min' => 1, 'scale_max' => 5, 'enps' => true],
        ]);

        foreach ([
            ['label' => 'Certamente não',     'value' => 1],
            ['label' => 'Provavelmente não',  'value' => 2],
            ['label' => 'Não tenho certeza',  'value' => 3],
            ['label' => 'Provavelmente sim',  'value' => 4],
            ['label' => 'Certamente sim',     'value' => 5],
        ] as $optSort => $option) {
            DiagnosticQuestionOption::create([
                'diagnostic_question_id' => $enps->id,
                'content'                => $option['label'],
                'value'                  => $option['value'],
                'sort_order'             => $optSort + 1,
            ]);
        }

        $this->command?->info('AS SCORE® oficial criado: 5 índices + IOH + eNPS (26 perguntas).');
    }

    private function attachLikert(DiagnosticQuestion $question): void
    {
        foreach ($this->likert as $optSort => $option) {
            DiagnosticQuestionOption::create([
                'diagnostic_question_id' => $question->id,
                'content'                => $option['label'],
                'value'                  => $option['value'],
                'sort_order'             => $optSort + 1,
            ]);
        }
    }
}
