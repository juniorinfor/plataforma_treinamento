<?php

namespace Database\Seeders;

use App\Models\DiagnosticAssessment;
use App\Models\DiagnosticDimension;
use App\Models\DiagnosticQuestion;
use App\Models\DiagnosticQuestionOption;
use App\Models\DiagnosticTool;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Diagnóstico de Riscos Psicossociais e Bem-Estar — NR1.
 *
 * Pesquisa de autopercepção do colaborador (não é um checklist de compliance
 * do gestor), estruturada em 3 etapas conforme o material de referência
 * "Questionário Base — NR-1 / indicadores — Plataforma":
 *
 *   Etapa 1 — Avaliação do Bem-Estar Emocional
 *     Equilíbrio Emocional · Segurança Psicológica · Motivação e Engajamento
 *
 *   Etapa 2 — Avaliação do Ambiente de Trabalho
 *     Sentido de Pertencimento · Condições de Trabalho · Reconhecimento e Valorização
 *
 *   Etapa 3 — Avaliação de Fatores de Risco Psicossocial
 *     Sobrecarga de Trabalho · Pressão e Estresse · Comunicação e Feedback
 *
 * Cada competência tem 2 perguntas, escala de frequência (Nunca → Sempre),
 * pontuação = média simples das respostas (sem peso), conforme o material.
 * As perguntas de "Pressão e Estresse" são formuladas de forma negativa
 * ("...contribui para o meu estresse") e por isso usam reverse_scored=true,
 * para que "Sempre" penalize a pontuação em vez de favorecê-la.
 *
 * Resultado confidencial e agregado (mín. 5 respostas) para preservar o
 * anonimato do colaborador e viabilizar respostas honestas — este é
 * justamente o "termômetro organizacional contínuo" que sustenta a
 * conformidade preventiva exigida pela NR-1 (Portaria MTE nº 1.419/2024).
 *
 * Idempotente com segurança de dados: se já existirem avaliações respondidas
 * para o NR1, o seeder não mexe em nada (evita corromper histórico real).
 * Caso contrário, recria a estrutura do zero a cada execução.
 */
class Nr1DiagnosticSeeder extends Seeder
{
    /** Escala de frequência (Nunca → Sempre). */
    private array $scale = [
        ['label' => 'Nunca',          'value' => 1],
        ['label' => 'Raramente',      'value' => 2],
        ['label' => 'Às vezes',       'value' => 3],
        ['label' => 'Frequentemente', 'value' => 4],
        ['label' => 'Sempre',         'value' => 5],
    ];

    public function run(): void
    {
        $existing = DiagnosticTool::where('code', 'NR1')->first();

        if ($existing) {
            if (DiagnosticAssessment::where('diagnostic_tool_id', $existing->id)->exists()) {
                $this->command?->warn('Diagnóstico NR1 já possui avaliações respondidas — seeder ignorado para não corromper dados.');
                return;
            }

            $existing->delete(); // cascade: dimensões, perguntas e opções
            $this->command?->info('Diagnóstico NR1 anterior removido — recriando com a nova estrutura de 3 etapas.');
        }

        $admin = User::where('role', 'platform_admin')->first()
            ?? User::where('role', 'company_admin')->first();

        $tool = DiagnosticTool::create([
            'company_id'        => null,
            'created_by'        => $admin?->id,
            'code'              => 'NR1',
            'name'              => 'Diagnóstico de Riscos Psicossociais e Bem-Estar (NR-1)',
            'slug'              => 'diagnostico-riscos-psicossociais-nr1',
            'short_description' => 'Pesquisa confidencial de bem-estar emocional, ambiente de trabalho e fatores de risco psicossocial, alinhada à NR-1.',
            'description'       => 'Pesquisa de autopercepção do colaborador em 3 etapas: Bem-Estar Emocional, '
                . 'Ambiente de Trabalho e Fatores de Risco Psicossocial. As respostas são confidenciais e o '
                . 'resultado é sempre agregado por equipe/empresa, nunca individual — o que garante honestidade '
                . 'nas respostas. Funciona como um termômetro organizacional contínuo, gerando evidências de '
                . 'monitoramento e histórico de evolução exigidos pela atualização da NR-1 (Portaria MTE nº '
                . '1.419/2024), servindo de apoio complementar ao PGR na identificação e prevenção de riscos '
                . 'psicossociais antes que se tornem afastamentos, conflitos ou turnover.',
            'type'              => 'simple',
            'input_source'      => 'questionnaire',
            'result_mode'       => 'aggregated',
            'is_confidential'   => true,
            'min_responses'     => 5,
            'requires_review'   => false,
            'icon'              => 'shield-check',
            'color'             => '#0D9488',
            'estimated_minutes' => 8,
            'is_published'      => true,
            'is_platform_tool'  => true,
            'sort_order'        => 3,
            'xp_reward'         => 60,
            'settings'          => [
                'scale_kind'  => 'frequency',
                'legal_basis' => 'NR1 / Portaria MTE nº 1.419/2024',
                'stages'      => [
                    ['name' => 'Bem-Estar Emocional', 'dimension_codes' => ['EMO', 'SPS', 'MOT']],
                    ['name' => 'Ambiente de Trabalho', 'dimension_codes' => ['PER', 'CDT', 'REC']],
                    ['name' => 'Fatores de Risco Psicossocial', 'dimension_codes' => ['SOB', 'PRE', 'COM']],
                ],
            ],
            'ai_prompt' => $this->aiPrompt(),
        ]);

        foreach ($this->dimensions() as $dSort => $dim) {
            $dimension = DiagnosticDimension::create([
                'diagnostic_tool_id' => $tool->id,
                'code'               => $dim['code'],
                'name'               => $dim['name'],
                'slug'               => Str::slug($dim['code']),
                'description'        => $dim['description'],
                'color'              => $dim['color'],
                'weight'             => 1.00,
                'sort_order'         => $dSort + 1,
            ]);

            foreach ($dim['questions'] as $qSort => $q) {
                $question = DiagnosticQuestion::create([
                    'diagnostic_tool_id'      => $tool->id,
                    'diagnostic_dimension_id' => $dimension->id,
                    'type'                    => 'scale',
                    'content'                 => $q['content'],
                    'help_text'               => $q['help'] ?? null,
                    'is_required'             => true,
                    'reverse_scored'          => $q['reverse'] ?? false,
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

        $this->command?->info('Diagnóstico NR1 criado: 3 etapas, 9 competências, 18 perguntas.');
    }

    /**
     * @return array<int, array{code: string, name: string, description: string, color: string, questions: array}>
     */
    private function dimensions(): array
    {
        return [
            // ── ETAPA 1 — Avaliação do Bem-Estar Emocional ──────────────
            [
                'code' => 'EMO', 'name' => 'Equilíbrio Emocional',
                'color' => '#0EA5E9',
                'description' => 'Etapa 1 — Bem-Estar Emocional. Avalia a capacidade do colaborador de lidar '
                    . 'com desafios emocionais e manter equilíbrio entre vida profissional e pessoal.',
                'questions' => [
                    ['content' => 'Sinto que posso expressar minhas opiniões e dificuldades no trabalho.'],
                    ['content' => 'Consigo manter um equilíbrio entre trabalho e vida pessoal.'],
                ],
            ],
            [
                'code' => 'SPS', 'name' => 'Segurança Psicológica',
                'color' => '#0284C7',
                'description' => 'Etapa 1 — Bem-Estar Emocional. Avalia se o colaborador se sente seguro para '
                    . 'compartilhar ideias e dificuldades sem medo de julgamento ou retaliação.',
                'questions' => [
                    ['content' => 'Sinto-me seguro(a) para compartilhar ideias sem medo de julgamento.'],
                    ['content' => 'Acredito que a empresa se preocupa com meu bem-estar emocional.'],
                ],
            ],
            [
                'code' => 'MOT', 'name' => 'Motivação e Engajamento',
                'color' => '#38BDF8',
                'description' => 'Etapa 1 — Bem-Estar Emocional. Avalia o nível de energia, motivação e '
                    . 'percepção de valorização do colaborador no dia a dia.',
                'questions' => [
                    ['content' => 'Sinto-me motivado(a) e energizado(a) para realizar meu trabalho diariamente.'],
                    ['content' => 'Acredito que minha contribuição é valorizada pela equipe.'],
                ],
            ],

            // ── ETAPA 2 — Avaliação do Ambiente de Trabalho ─────────────
            [
                'code' => 'PER', 'name' => 'Sentido de Pertencimento',
                'color' => '#10B981',
                'description' => 'Etapa 2 — Ambiente de Trabalho. Avalia o quanto o colaborador se sente parte '
                    . 'da equipe e incluído na cultura da empresa.',
                'questions' => [
                    ['content' => 'Sinto que faço parte da equipe e sou valorizado(a).'],
                    ['content' => 'A cultura da empresa promove um sentimento de inclusão.'],
                ],
            ],
            [
                'code' => 'CDT', 'name' => 'Condições de Trabalho',
                'color' => '#059669',
                'description' => 'Etapa 2 — Ambiente de Trabalho. Avalia a percepção sobre segurança, saúde e '
                    . 'suporte adequado para o desempenho das atividades.',
                'questions' => [
                    ['content' => 'Considero meu ambiente de trabalho saudável e seguro.'],
                    ['content' => 'Recebo apoio adequado para desempenhar minhas atividades.'],
                ],
            ],
            [
                'code' => 'REC', 'name' => 'Reconhecimento e Valorização',
                'color' => '#34D399',
                'description' => 'Etapa 2 — Ambiente de Trabalho. Avalia se o colaborador percebe reconhecimento '
                    . 'pela liderança e oportunidades reais de crescimento.',
                'questions' => [
                    ['content' => 'Acredito que meus esforços são reconhecidos pela liderança.'],
                    ['content' => 'Sinto que tenho oportunidades para crescimento e desenvolvimento.'],
                ],
            ],

            // ── ETAPA 3 — Avaliação de Fatores de Risco Psicossocial ────
            [
                'code' => 'SOB', 'name' => 'Sobrecarga de Trabalho',
                'color' => '#F59E0B',
                'description' => 'Etapa 3 — Fatores de Risco Psicossocial. Avalia se a carga e a jornada de '
                    . 'trabalho são compatíveis com a capacidade real de entrega do colaborador.',
                'questions' => [
                    ['content' => 'Consigo realizar minhas atividades dentro da jornada de trabalho sem sentir sobrecarga.'],
                    ['content' => 'Sinto que a carga de trabalho é gerenciável.'],
                ],
            ],
            [
                'code' => 'PRE', 'name' => 'Pressão e Estresse',
                'color' => '#EF4444',
                'description' => 'Etapa 3 — Fatores de Risco Psicossocial. Avalia o nível de pressão e estresse '
                    . 'gerado pelo ambiente e pelos prazos de trabalho. Perguntas formuladas de forma negativa '
                    . '(reverse_scored) — quanto maior a frequência relatada, pior o resultado.',
                'questions' => [
                    ['content' => 'O ambiente de trabalho contribui para o meu estresse.', 'reverse' => true],
                    ['content' => 'Sinto que há pressão excessiva para cumprir prazos.', 'reverse' => true],
                ],
            ],
            [
                'code' => 'COM', 'name' => 'Comunicação e Feedback',
                'color' => '#F97316',
                'description' => 'Etapa 3 — Fatores de Risco Psicossocial. Avalia a clareza da comunicação '
                    . 'interna e a qualidade do feedback recebido pelo colaborador.',
                'questions' => [
                    ['content' => 'A comunicação na equipe é clara e eficiente.'],
                    ['content' => 'Recebo feedback construtivo que me ajuda a melhorar.'],
                ],
            ],
        ];
    }

    /**
     * Instrução para geração do relatório de IA — embute o framework de
     * interpretação por faixa de pontuação do material de referência, para
     * que o relatório qualitativo fale a mesma língua do questionário.
     */
    private function aiPrompt(): string
    {
        return <<<PROMPT
Você é um consultor especialista em saúde ocupacional, riscos psicossociais e gestão de pessoas,
atuando como leitor técnico dos resultados da pesquisa de Bem-Estar e Riscos Psicossociais (NR-1)
de uma empresa. Esta pesquisa é confidencial e agregada — nunca identifique nem infira dados de
colaboradores individuais.

O questionário tem 3 etapas, cada uma com 3 competências avaliadas numa escala de frequência
(Nunca a Sempre, convertida em pontuação 0-100):

Etapa 1 — Bem-Estar Emocional: Equilíbrio Emocional, Segurança Psicológica, Motivação e Engajamento.
Etapa 2 — Ambiente de Trabalho: Sentido de Pertencimento, Condições de Trabalho, Reconhecimento e Valorização.
Etapa 3 — Fatores de Risco Psicossocial: Sobrecarga de Trabalho, Pressão e Estresse, Comunicação e Feedback.

Use este referencial de leitura por faixa de pontuação (não repita os números, use-os para calibrar o tom):
90-100 Excelente · 75-89 Saudável · 60-74 Atenção preventiva · 40-59 Risco moderado de desgaste · abaixo de 40 Risco elevado.

Escreva o relatório em português, com tom executivo, direto e construtivo, sempre orientado à ação
preventiva — nunca alarmista. Conecte explicitamente os pontos fracos identificados à necessidade de
ações preventivas e ao gerenciamento de riscos psicossociais previsto na NR-1 (Portaria MTE nº
1.419/2024), tratando o resultado como um retrato de um momento — um "termômetro organizacional" —
que deve orientar prioridades de gestão preventiva e não apenas registrar um diagnóstico isolado.
PROMPT;
    }
}
