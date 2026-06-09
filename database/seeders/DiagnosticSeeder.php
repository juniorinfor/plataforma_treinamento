<?php

namespace Database\Seeders;

use App\Models\AiProvider;
use App\Models\Company;
use App\Models\DiagnosticAnswer;
use App\Models\DiagnosticAssessment;
use App\Models\DiagnosticDimension;
use App\Models\DiagnosticQuestion;
use App\Models\DiagnosticQuestionOption;
use App\Models\DiagnosticResult;
use App\Models\DiagnosticTool;
use App\Models\DiagnosticToolComponent;
use App\Models\User;
use App\Services\DiagnosticActionPlanService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DiagnosticSeeder extends Seeder
{
    /**
     * Escala Likert padrão (1 a 5) reutilizada nas perguntas de índice.
     *
     * @var array<int, array{label: string, value: int}>
     */
    private array $likert = [
        ['label' => 'Discordo totalmente', 'value' => 1],
        ['label' => 'Discordo', 'value' => 2],
        ['label' => 'Neutro', 'value' => 3],
        ['label' => 'Concordo', 'value' => 4],
        ['label' => 'Concordo totalmente', 'value' => 5],
    ];

    public function run(): void
    {
        $admin = User::where('role', 'company_admin')->first();
        $company = Company::first();
        $employee = User::where('role', 'employee')->first();

        // -------------------------------------------------------------
        // Camada de IA plugável — slot configurável (sem credenciais ainda)
        // -------------------------------------------------------------
        AiProvider::create([
            'name' => 'Especialista de Diagnóstico (a definir)',
            'driver' => 'claude',
            'model' => null,
            'api_key' => null,
            'endpoint' => null,
            'max_tokens' => 4096,
            'temperature' => 0.70,
            'is_active' => false,
            'settings' => ['note' => 'Provedor a ser escolhido conforme custo. Preencher api_key/model depois.'],
        ]);

        // -------------------------------------------------------------
        // 5 índices do AS SCORE® (ferramentas simples, base do IO)
        // -------------------------------------------------------------
        $indices = [
            [
                'code' => 'LTI', 'name' => 'Índice de Confiança na Liderança',
                'short' => 'Leadership Trust Index — confiança nas lideranças.',
                'color' => '#6366F1', 'icon' => 'shield-check', 'weight' => 1.20,
                'questions' => [
                    'Confio nas decisões tomadas pela minha liderança direta.',
                    'Minha liderança age com coerência entre o que diz e o que faz.',
                    'Sinto que posso expor problemas à liderança sem medo de represálias.',
                    'A liderança reconhece os erros e assume responsabilidade por eles.',
                ],
            ],
            [
                'code' => 'OCI', 'name' => 'Índice de Clima Organizacional',
                'short' => 'Organizational Climate Index — clima e ambiente de trabalho.',
                'color' => '#10B981', 'icon' => 'sun', 'weight' => 1.00,
                'questions' => [
                    'O ambiente de trabalho é colaborativo e respeitoso.',
                    'Sinto-me motivado(a) com o meu trabalho no dia a dia.',
                    'A comunicação entre as equipes flui de forma clara.',
                    'Existe equilíbrio saudável entre vida pessoal e profissional.',
                ],
            ],
            [
                'code' => 'RBI', 'name' => 'Índice de Reconhecimento e Pertencimento',
                'short' => 'Recognition & Belonging Index — reconhecimento e pertencimento.',
                'color' => '#F59E0B', 'icon' => 'heart', 'weight' => 1.00,
                'questions' => [
                    'Meu trabalho é reconhecido de forma justa.',
                    'Sinto que faço parte e pertenço a esta organização.',
                    'Recebo feedbacks que contribuem para o meu desenvolvimento.',
                    'Tenho orgulho de dizer onde trabalho.',
                ],
            ],
            [
                'code' => 'SEI', 'name' => 'Índice de Execução Estratégica',
                'short' => 'Strategic Execution Index — clareza e execução da estratégia.',
                'color' => '#3B82F6', 'icon' => 'flag', 'weight' => 1.10,
                'questions' => [
                    'Compreendo claramente os objetivos estratégicos da empresa.',
                    'Sei como meu trabalho contribui para as metas da organização.',
                    'As prioridades são bem definidas e comunicadas.',
                    'Temos os recursos necessários para executar o que é planejado.',
                ],
            ],
            [
                'code' => 'LII', 'name' => 'Índice de Influência da Liderança',
                'short' => 'Leadership Influence Index — influência e inspiração das lideranças.',
                'color' => '#8B5CF6', 'icon' => 'sparkles', 'weight' => 1.10,
                'questions' => [
                    'Minha liderança me inspira a dar o melhor de mim.',
                    'A liderança estimula a inovação e novas ideias.',
                    'Sou incentivado(a) a desenvolver minhas competências.',
                    'A liderança influencia positivamente a cultura da equipe.',
                ],
            ],
        ];

        $childTools = [];

        foreach ($indices as $sort => $index) {
            $tool = DiagnosticTool::create([
                'company_id' => null,
                'created_by' => $admin?->id,
                'code' => $index['code'],
                'name' => $index['name'],
                'slug' => Str::slug($index['code'] . '-' . $index['name']),
                'short_description' => $index['short'],
                'description' => 'Componente do Diagnóstico de Inteligência Organizacional (AS SCORE®).',
                'type' => 'simple',
                'input_source' => 'questionnaire',
                'requires_review' => false,
                'icon' => $index['icon'],
                'color' => $index['color'],
                'estimated_minutes' => 5,
                'is_published' => true,
                'is_platform_tool' => true,
                'sort_order' => $sort + 1,
                'xp_reward' => 30,
            ]);

            $dimension = DiagnosticDimension::create([
                'diagnostic_tool_id' => $tool->id,
                'code' => $index['code'],
                'name' => $index['name'],
                'slug' => Str::slug($index['code']),
                'color' => $index['color'],
                'weight' => 1,
                'sort_order' => 1,
            ]);

            foreach ($index['questions'] as $qSort => $content) {
                $question = DiagnosticQuestion::create([
                    'diagnostic_tool_id' => $tool->id,
                    'diagnostic_dimension_id' => $dimension->id,
                    'type' => 'scale',
                    'content' => $content,
                    'is_required' => true,
                    'reverse_scored' => false,
                    'weight' => 1,
                    'sort_order' => $qSort + 1,
                    'settings' => ['scale_min' => 1, 'scale_max' => 5],
                ]);

                foreach ($this->likert as $optSort => $option) {
                    DiagnosticQuestionOption::create([
                        'diagnostic_question_id' => $question->id,
                        'content' => $option['label'],
                        'value' => $option['value'],
                        'sort_order' => $optSort + 1,
                    ]);
                }
            }

            $childTools[$index['code']] = ['tool' => $tool, 'dimension' => $dimension, 'weight' => $index['weight']];
        }

        // -------------------------------------------------------------
        // Ferramenta composta: Diagnóstico de Inteligência Organizacional
        // -------------------------------------------------------------
        $io = DiagnosticTool::create([
            'company_id' => null,
            'created_by' => $admin?->id,
            'code' => 'IO',
            'name' => 'Diagnóstico de Inteligência Organizacional',
            'slug' => 'diagnostico-inteligencia-organizacional',
            'short_description' => 'Visão 360° da organização através do AS SCORE® (5 índices integrados).',
            'description' => 'Reúne os índices LTI, OCI, RBI, SEI e LII para gerar o AS SCORE® — uma '
                . 'medida integrada da inteligência organizacional. Pode ser aplicado por completo '
                . 'ou por índices separados, conforme a jornada (START, STRATEGIC, TRANSFORMATION, CONTINUOUS).',
            'type' => 'composite',
            'input_source' => 'questionnaire',
            'requires_review' => true,
            'icon' => 'chart-pie',
            'color' => '#4F46E5',
            'estimated_minutes' => 25,
            'is_published' => true,
            'is_platform_tool' => true,
            'sort_order' => 1,
            'xp_reward' => 120,
            'settings' => ['tiers' => ['start', 'strategic', 'transformation', 'continuous']],
        ]);

        foreach (array_values($childTools) as $sort => $child) {
            DiagnosticToolComponent::create([
                'parent_tool_id' => $io->id,
                'child_tool_id' => $child['tool']->id,
                'weight' => $child['weight'],
                'sort_order' => $sort + 1,
            ]);
        }

        // -------------------------------------------------------------
        // Ferramenta: Executive Mapping (upload de laudo + questionário opcional)
        // -------------------------------------------------------------
        DiagnosticTool::create([
            'company_id' => null,
            'created_by' => $admin?->id,
            'code' => 'EXEC_MAP',
            'name' => 'Executive Mapping',
            'slug' => 'executive-mapping',
            'short_description' => 'Mapeamento individual de perfil executivo com análise especializada.',
            'description' => 'Diagnóstico individual de perfil e potencial executivo. Permite o upload '
                . 'de laudos externos (ex.: HumanGuide) e/ou o preenchimento de questionário complementar. '
                . 'O resultado passa por revisão de um consultor antes da publicação.',
            'type' => 'simple',
            'input_source' => 'both',
            'requires_review' => true,
            'icon' => 'user-circle',
            'color' => '#0EA5E9',
            'estimated_minutes' => 15,
            'is_published' => true,
            'is_platform_tool' => true,
            'sort_order' => 2,
            'xp_reward' => 80,
        ]);

        // -------------------------------------------------------------
        // Aplicação demo concluída do IO para um colaborador
        // -------------------------------------------------------------
        if ($employee && $company) {
            $assessment = DiagnosticAssessment::create([
                'diagnostic_tool_id' => $io->id,
                'user_id' => $employee->id,
                'company_id' => $company->id,
                'department_id' => null,
                'assigned_by' => $admin?->id,
                'tier' => 'strategic',
                'status' => 'completed',
                'started_at' => now()->subDays(3),
                'submitted_at' => now()->subDays(3),
                'completed_at' => now()->subDays(2),
            ]);

            $weightedSum = 0;
            $weightTotal = 0;

            foreach ($childTools as $child) {
                /** @var DiagnosticTool $tool */
                $tool = $child['tool'];
                $questions = $tool->questions()->with('options')->get();

                $rawScore = 0;
                $maxScore = 0;

                foreach ($questions as $question) {
                    // Resposta demo: valores plausíveis entre 3 e 5
                    $chosenValue = rand(3, 5);
                    $option = $question->options->firstWhere('value', $chosenValue)
                        ?? $question->options->sortByDesc('value')->first();

                    DiagnosticAnswer::create([
                        'diagnostic_assessment_id' => $assessment->id,
                        'diagnostic_question_id' => $question->id,
                        'diagnostic_question_option_id' => $option?->id,
                        'numeric_value' => $option?->value ?? $chosenValue,
                    ]);

                    $rawScore += (float) ($option?->value ?? $chosenValue);
                    $maxScore += 5;
                }

                $normalized = $maxScore > 0 ? round(($rawScore / $maxScore) * 100, 2) : 0;

                DiagnosticResult::create([
                    'diagnostic_assessment_id' => $assessment->id,
                    'component_tool_id' => $tool->id,
                    'diagnostic_dimension_id' => $child['dimension']->id,
                    'raw_score' => $rawScore,
                    'max_score' => $maxScore,
                    'normalized_score' => $normalized,
                    'label' => $this->scoreLabel($normalized),
                ]);

                $weightedSum += $normalized * $child['weight'];
                $weightTotal += $child['weight'];
            }

            $globalScore = $weightTotal > 0 ? round($weightedSum / $weightTotal, 2) : 0;

            $assessment->update([
                'global_score' => $globalScore,
                'global_label' => $this->scoreLabel($globalScore),
            ]);

            // Gera plano de ação para o assessment de demonstração
            app(DiagnosticActionPlanService::class)->generate($assessment->fresh());
        }
    }

    private function scoreLabel(float $score): string
    {
        return match (true) {
            $score >= 85 => 'Excelente',
            $score >= 70 => 'Saudável',
            $score >= 55 => 'Atenção',
            $score >= 40 => 'Crítico',
            default => 'Vulnerável',
        };
    }
}
