<?php

namespace App\Services;

use App\Enums\DiagnosticAssessmentStatus;
use App\Enums\DiagnosticToolType;
use App\Models\DiagnosticAnswer;
use App\Models\DiagnosticAssessment;
use App\Models\DiagnosticQuestionOption;
use App\Models\DiagnosticResult;
use App\Models\DiagnosticTool;

class DiagnosticScoringService
{
    public function __construct(
        private readonly DiagnosticActionPlanService  $planService,
        private readonly DiagnosticReportService      $reportService,
    ) {}

    /**
     * Persiste as respostas, computa os scores por dimensão/componente
     * e atualiza o status do assessment para "completed".
     *
     * @param  DiagnosticAssessment  $assessment
     * @param  array<int, int>  $rawAnswers  [question_id => option_id]
     */
    public function score(DiagnosticAssessment $assessment, array $rawAnswers): void
    {
        // Resolve option values once (evita N+1)
        $optionIds = array_values(array_filter($rawAnswers));
        $optionValues = DiagnosticQuestionOption::whereIn('id', $optionIds)
            ->pluck('value', 'id');

        // Persiste respostas
        foreach ($rawAnswers as $questionId => $optionId) {
            if (!$optionId) {
                continue;
            }
            DiagnosticAnswer::updateOrCreate(
                [
                    'diagnostic_assessment_id' => $assessment->id,
                    'diagnostic_question_id' => $questionId,
                ],
                [
                    'diagnostic_question_option_id' => $optionId,
                    'numeric_value' => $optionValues[$optionId] ?? 0,
                ]
            );
        }

        $tool = DiagnosticTool::with([
            'children' => fn ($q) => $q->withPivot(['weight', 'sort_order'])->orderByPivot('sort_order'),
            'children.dimensions.questions',
            'dimensions.questions',
        ])->find($assessment->diagnostic_tool_id);

        // Remove resultados anteriores (re-score em caso de revisão)
        $assessment->results()->delete();

        if ($tool->type === DiagnosticToolType::Composite) {
            $globalScore = $this->scoreComposite($assessment, $tool, $rawAnswers, $optionValues);
        } else {
            $globalScore = $this->scoreSimple($assessment, $tool, $rawAnswers, $optionValues);
        }

        $assessment->update([
            'status' => DiagnosticAssessmentStatus::Completed,
            'global_score' => $globalScore,
            'global_label' => $this->label($globalScore),
            'submitted_at' => $assessment->submitted_at ?? now(),
            'completed_at' => now(),
        ]);

        $fresh = $assessment->fresh();

        // Gera plano de ação personalizado automaticamente
        $this->planService->generate($fresh);

        // Cria/inicia relatório de IA (dispara geração se houver provider ativo)
        $this->reportService->initReport($fresh);
    }

    // ------------------------------------------------------------------

    private function scoreComposite(
        DiagnosticAssessment $assessment,
        DiagnosticTool $tool,
        array $rawAnswers,
        \Illuminate\Support\Collection $optionValues
    ): float {
        $weightedSum = 0.0;
        $weightTotal = 0.0;

        foreach ($tool->children as $child) {
            $questions = $child->dimensions->flatMap(fn ($d) => $d->questions);

            [$rawScore, $maxScore] = $this->computeRawMax($questions, $rawAnswers, $optionValues);
            $normalized = $maxScore > 0 ? round(($rawScore / $maxScore) * 100, 2) : 0;

            DiagnosticResult::create([
                'diagnostic_assessment_id' => $assessment->id,
                'component_tool_id' => $child->id,
                'diagnostic_dimension_id' => $child->dimensions->first()?->id,
                'raw_score' => $rawScore,
                'max_score' => $maxScore,
                'normalized_score' => $normalized,
                'label' => $this->label($normalized),
            ]);

            $weight = (float) $child->pivot->weight;
            $weightedSum += $normalized * $weight;
            $weightTotal += $weight;
        }

        return $weightTotal > 0 ? round($weightedSum / $weightTotal, 2) : 0;
    }

    private function scoreSimple(
        DiagnosticAssessment $assessment,
        DiagnosticTool $tool,
        array $rawAnswers,
        \Illuminate\Support\Collection $optionValues
    ): float {
        $dimensions = $tool->dimensions;

        if ($dimensions->isEmpty()) {
            // Sem dimensões: score único global
            $questions = $tool->questions;
            [$rawScore, $maxScore] = $this->computeRawMax($questions, $rawAnswers, $optionValues);
            $normalized = $maxScore > 0 ? round(($rawScore / $maxScore) * 100, 2) : 0;

            DiagnosticResult::create([
                'diagnostic_assessment_id' => $assessment->id,
                'component_tool_id' => null,
                'diagnostic_dimension_id' => null,
                'raw_score' => $rawScore,
                'max_score' => $maxScore,
                'normalized_score' => $normalized,
                'label' => $this->label($normalized),
            ]);

            return $normalized;
        }

        $weightedSum = 0.0;
        $weightTotal = 0.0;

        foreach ($dimensions as $dimension) {
            // Dimensões derivadas (ex.: IOH, calculado à parte) ou informativas
            // (peso 0, ex.: eNPS) não compõem a nota dos índices.
            if ($dimension->code === 'IOH' || (float) $dimension->weight <= 0) {
                continue;
            }

            $questions = $dimension->questions;
            if ($questions->isEmpty()) {
                continue;
            }

            [$rawScore, $maxScore] = $this->computeRawMax($questions, $rawAnswers, $optionValues);
            $normalized = $maxScore > 0 ? round(($rawScore / $maxScore) * 100, 2) : 0;

            DiagnosticResult::create([
                'diagnostic_assessment_id' => $assessment->id,
                'component_tool_id' => null,
                'diagnostic_dimension_id' => $dimension->id,
                'raw_score' => $rawScore,
                'max_score' => $maxScore,
                'normalized_score' => $normalized,
                'label' => $this->label($normalized),
            ]);

            $weight = (float) $dimension->weight;
            $weightedSum += $normalized * $weight;
            $weightTotal += $weight;
        }

        // Índice derivado (ex.: IOH): perguntas marcadas com settings.ioh = true,
        // espalhadas pelos índices. Não entra na média global — é indicador à parte.
        $this->scoreDerivedIndex($assessment, $dimensions, $rawAnswers, $optionValues);

        return $weightTotal > 0 ? round($weightedSum / $weightTotal, 2) : 0;
    }

    /**
     * Computa o índice derivado (IOH) a partir das perguntas marcadas
     * (settings.ioh = true) e o persiste vinculado à dimensão de código 'IOH'.
     */
    private function scoreDerivedIndex(
        DiagnosticAssessment $assessment,
        $dimensions,
        array $rawAnswers,
        \Illuminate\Support\Collection $optionValues
    ): void {
        $iohDimension = $dimensions->firstWhere('code', 'IOH');
        if (!$iohDimension) {
            return;
        }

        $iohQuestions = $dimensions
            ->flatMap(fn ($d) => $d->questions)
            ->filter(fn ($q) => (bool) data_get($q->settings, 'ioh') === true)
            ->values();

        if ($iohQuestions->isEmpty()) {
            return;
        }

        [$rawScore, $maxScore] = $this->computeRawMax($iohQuestions, $rawAnswers, $optionValues);
        $normalized = $maxScore > 0 ? round(($rawScore / $maxScore) * 100, 2) : 0;

        DiagnosticResult::create([
            'diagnostic_assessment_id' => $assessment->id,
            'component_tool_id' => null,
            'diagnostic_dimension_id' => $iohDimension->id,
            'raw_score' => $rawScore,
            'max_score' => $maxScore,
            'normalized_score' => $normalized,
            'label' => $this->label($normalized),
        ]);
    }

    /**
     * Calcula raw_score e max_score para um conjunto de questões.
     * Aplica reverse_scored se necessário (ex.: item invertido na escala Likert).
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $questions
     * @param  array<int, int>  $rawAnswers  [question_id => option_id]
     * @param  \Illuminate\Support\Collection  $optionValues
     * @return array{0: float, 1: float}  [raw_score, max_score]
     */
    private function computeRawMax(
        $questions,
        array $rawAnswers,
        \Illuminate\Support\Collection $optionValues
    ): array {
        $rawScore = 0.0;
        $maxScore = 0.0;

        foreach ($questions as $question) {
            $weight = (float) $question->weight;
            $maxForQuestion = 5 * $weight; // escala padrão 1-5; ajustável via settings
            $maxScore += $maxForQuestion;

            $optionId = $rawAnswers[$question->id] ?? null;
            if (!$optionId) {
                continue;
            }

            $value = (float) ($optionValues[$optionId] ?? 0);

            if ($question->reverse_scored) {
                $value = 6 - $value; // inverte escala 1-5
            }

            $rawScore += $value * $weight;
        }

        return [$rawScore, $maxScore];
    }

    public function label(float $score): string
    {
        return match (true) {
            $score >= 85 => 'Excelente',
            $score >= 70 => 'Saudável',
            $score >= 55 => 'Atenção',
            $score >= 40 => 'Crítico',
            default      => 'Vulnerável',
        };
    }
}
