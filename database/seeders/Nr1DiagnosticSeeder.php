<?php

namespace Database\Seeders;

use App\Models\DiagnosticDimension;
use App\Models\DiagnosticQuestion;
use App\Models\DiagnosticQuestionOption;
use App\Models\DiagnosticTool;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Diagnóstico de Conformidade com a NR1 (3º banner).
 *
 * Avalia o grau de implementação dos requisitos da Norma Regulamentadora nº 1
 * — Disposições Gerais e Gerenciamento de Riscos Ocupacionais (GRO/PGR) —
 * incluindo a gestão de riscos psicossociais (Portaria MTE nº 1.419/2024).
 *
 * Escala de conformidade (1 a 5):
 *   1 Não atende · 2 Inicial · 3 Parcial · 4 Adequado · 5 Pleno
 *
 * Idempotente: se já existir uma ferramenta com code "NR1", não recria.
 */
class Nr1DiagnosticSeeder extends Seeder
{
    /**
     * Escala de grau de implementação (substitui a Likert de concordância).
     *
     * @var array<int, array{label: string, value: int}>
     */
    private array $scale = [
        ['label' => 'Não atende',  'value' => 1],
        ['label' => 'Inicial',     'value' => 2],
        ['label' => 'Parcial',     'value' => 3],
        ['label' => 'Adequado',    'value' => 4],
        ['label' => 'Pleno',       'value' => 5],
    ];

    public function run(): void
    {
        // Idempotência — evita duplicar se rodar novamente
        if (DiagnosticTool::where('code', 'NR1')->exists()) {
            $this->command?->warn('Diagnóstico NR1 já existe — seeder ignorado.');
            return;
        }

        $admin = User::where('role', 'platform_admin')->first()
            ?? User::where('role', 'company_admin')->first();

        $tool = DiagnosticTool::create([
            'company_id'        => null,
            'created_by'        => $admin?->id,
            'code'              => 'NR1',
            'name'              => 'Diagnóstico de Conformidade com a NR1',
            'slug'              => 'diagnostico-conformidade-nr1',
            'short_description' => 'Avalie o nível de conformidade da sua empresa com a NR1 (GRO/PGR) e os riscos psicossociais.',
            'description'       => 'Diagnóstico de maturidade e conformidade com a Norma Regulamentadora nº 1 '
                . '(Disposições Gerais e Gerenciamento de Riscos Ocupacionais). Avalia a estrutura do '
                . 'Programa de Gerenciamento de Riscos (PGR), a identificação e avaliação de perigos, a '
                . 'gestão de riscos psicossociais (incorporada pela Portaria MTE nº 1.419/2024), a '
                . 'capacitação dos trabalhadores e a documentação e responsabilidades legais. O resultado '
                . 'aponta o grau de implementação por área e gera um plano de ação para adequação.',
            'type'              => 'simple',
            'input_source'      => 'questionnaire',
            'requires_review'   => false,
            'icon'              => 'shield-check',
            'color'             => '#0D9488', // teal — leitura de "auditoria/conformidade"
            'estimated_minutes' => 10,
            'is_published'      => true,
            'is_platform_tool'  => true,
            'sort_order'        => 3, // terceiro banner
            'xp_reward'         => 60,
            'settings'          => [
                'scale_kind' => 'implementation',
                'legal_basis' => 'NR1 / Portaria MTE nº 1.419/2024',
            ],
        ]);

        $dimensions = [
            [
                'code' => 'GRO', 'name' => 'Gerenciamento de Riscos (PGR)',
                'color' => '#0D9488', 'weight' => 1.20,
                'questions' => [
                    [
                        'A empresa possui um Programa de Gerenciamento de Riscos (PGR) formalizado e atualizado.',
                        'O PGR é o documento central exigido pela NR1 para o gerenciamento de riscos ocupacionais.',
                    ],
                    [
                        'O PGR contém o inventário de riscos e o respectivo plano de ação.',
                        'A NR1 exige que o PGR seja composto por inventário de riscos e plano de ação.',
                    ],
                    [
                        'O plano de ação possui responsáveis, prazos e acompanhamento das medidas.',
                        'Medidas de prevenção devem ter cronograma, responsáveis e formas de aferição.',
                    ],
                    [
                        'O PGR é revisado periodicamente e sempre que há mudanças nos processos ou após incidentes.',
                        'A revisão deve ocorrer no mínimo a cada 2 anos (ou 3 anos com SST certificado) e após eventos relevantes.',
                    ],
                ],
            ],
            [
                'code' => 'IDR', 'name' => 'Identificação e Avaliação de Riscos',
                'color' => '#0EA5E9', 'weight' => 1.10,
                'questions' => [
                    [
                        'Os perigos e fatores de risco das atividades são sistematicamente identificados.',
                        'Inclui riscos físicos, químicos, biológicos, ergonômicos e de acidentes.',
                    ],
                    [
                        'Os riscos são avaliados quanto à probabilidade e à severidade (gradação do risco).',
                        'A avaliação deve permitir priorizar as medidas de controle.',
                    ],
                    [
                        'São adotadas medidas de prevenção seguindo a hierarquia de controle.',
                        'Eliminação → controles de engenharia → administrativos → EPI, nessa ordem de prioridade.',
                    ],
                    [
                        'A eficácia das medidas de controle implementadas é monitorada.',
                        'Verifica-se se as medidas adotadas realmente reduziram o risco.',
                    ],
                ],
            ],
            [
                'code' => 'PSI', 'name' => 'Riscos Psicossociais',
                'color' => '#8B5CF6', 'weight' => 1.10,
                'questions' => [
                    [
                        'A empresa identifica fatores de risco psicossocial (sobrecarga, assédio, jornada, pressão).',
                        'A Portaria MTE nº 1.419/2024 incorporou os riscos psicossociais ao gerenciamento do PGR.',
                    ],
                    [
                        'Os riscos psicossociais estão contemplados no inventário de riscos do PGR.',
                        'Devem ser tratados com o mesmo rigor dos demais riscos ocupacionais.',
                    ],
                    [
                        'Existem canais de escuta e acolhimento para situações psicossociais.',
                        'Ex.: canal de denúncia de assédio, apoio psicológico, ouvidoria.',
                    ],
                    [
                        'São adotadas ações preventivas para saúde mental e bem-estar no trabalho.',
                        'Ex.: gestão de carga de trabalho, programas de qualidade de vida, capacitação de líderes.',
                    ],
                ],
            ],
            [
                'code' => 'CAP', 'name' => 'Capacitação e Treinamentos',
                'color' => '#F59E0B', 'weight' => 1.00,
                'questions' => [
                    [
                        'Os trabalhadores recebem capacitação sobre os riscos das suas atividades.',
                        'A NR1 obriga a informação e capacitação dos trabalhadores quanto aos riscos.',
                    ],
                    [
                        'Os treinamentos obrigatórios (inicial, periódico, eventual) são realizados nos prazos.',
                        'Cada NR específica define a carga horária e periodicidade dos treinamentos.',
                    ],
                    [
                        'Há registro/controle dos treinamentos realizados (listas, certificados, conteúdo).',
                        'A comprovação documental é exigida em fiscalizações.',
                    ],
                    [
                        'As lideranças são preparadas para atuar na prevenção e na gestão de riscos.',
                        'Inclui riscos psicossociais e o papel do gestor na cultura de segurança.',
                    ],
                ],
            ],
            [
                'code' => 'DOC', 'name' => 'Documentação, Responsabilidades e Participação',
                'color' => '#6366F1', 'weight' => 1.00,
                'questions' => [
                    [
                        'A documentação de SST está organizada, atualizada e acessível.',
                        'PGR, ASO, ordens de serviço, laudos e registros devem estar disponíveis.',
                    ],
                    [
                        'As responsabilidades de empregador e trabalhadores estão definidas e comunicadas.',
                        'A NR1 estabelece deveres e direitos de ambas as partes.',
                    ],
                    [
                        'Os trabalhadores são informados sobre os riscos e medidas de prevenção (ordens de serviço).',
                        'A informação formal aos trabalhadores é uma obrigação prevista na NR1.',
                    ],
                    [
                        'Há participação dos trabalhadores/CIPA na identificação e controle de riscos.',
                        'A consulta e participação fortalecem o gerenciamento de riscos.',
                    ],
                ],
            ],
        ];

        foreach ($dimensions as $dSort => $dim) {
            $dimension = DiagnosticDimension::create([
                'diagnostic_tool_id' => $tool->id,
                'code'               => $dim['code'],
                'name'               => $dim['name'],
                'slug'               => Str::slug($dim['code']),
                'color'              => $dim['color'],
                'weight'             => $dim['weight'],
                'sort_order'         => $dSort + 1,
            ]);

            foreach ($dim['questions'] as $qSort => [$content, $help]) {
                $question = DiagnosticQuestion::create([
                    'diagnostic_tool_id'      => $tool->id,
                    'diagnostic_dimension_id' => $dimension->id,
                    'type'                    => 'scale',
                    'content'                 => $content,
                    'help_text'               => $help,
                    'is_required'             => true,
                    'reverse_scored'          => false,
                    'weight'                  => 1,
                    'sort_order'              => $qSort + 1,
                    'settings'                => ['scale_min' => 1, 'scale_max' => 5],
                ]);

                foreach ($this->scale as $optSort => $option) {
                    DiagnosticQuestionOption::create([
                        'diagnostic_question_id' => $question->id,
                        'content'                => $option['label'],
                        'value'                  => $option['value'],
                        'sort_order'             => $optSort + 1,
                    ]);
                }
            }
        }

        $this->command?->info('Diagnóstico NR1 criado: 5 dimensões, 20 perguntas.');
    }
}
