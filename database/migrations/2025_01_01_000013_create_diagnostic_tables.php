<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Provedores de IA (camada plugável — credenciais configuráveis)
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('driver')->default('claude'); // claude, openai, ...
            $table->string('model')->nullable();
            $table->text('api_key')->nullable(); // criptografado via cast no model
            $table->string('endpoint')->nullable();
            $table->integer('max_tokens')->default(4096);
            $table->decimal('temperature', 3, 2)->default(0.70);
            $table->boolean('is_active')->default(false);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        // Ferramentas de diagnóstico (motor genérico configurável)
        Schema::create('diagnostic_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ai_provider_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->nullable(); // IO, LTI, OCI, EXEC_MAP...
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->default('simple');          // simple | composite
            $table->string('input_source')->default('questionnaire'); // questionnaire | upload | both
            $table->boolean('requires_review')->default(false);
            $table->string('icon')->nullable();
            $table->string('color', 7)->nullable();
            $table->integer('estimated_minutes')->default(10);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_platform_tool')->default(false);
            $table->integer('sort_order')->default(0);
            $table->integer('xp_reward')->default(50);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'is_published']);
        });

        // Composição de ferramentas (ex.: IO reúne LTI, OCI, RBI, SEI, LII)
        Schema::create('diagnostic_tool_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_tool_id')->constrained('diagnostic_tools')->cascadeOnDelete();
            $table->foreignId('child_tool_id')->constrained('diagnostic_tools')->cascadeOnDelete();
            $table->decimal('weight', 5, 2)->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['parent_tool_id', 'child_tool_id']);
        });

        // Dimensões / eixos medidos dentro de uma ferramenta
        Schema::create('diagnostic_dimensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostic_tool_id')->constrained()->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color', 7)->nullable();
            $table->decimal('weight', 5, 2)->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Perguntas dos questionários
        Schema::create('diagnostic_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostic_tool_id')->constrained()->cascadeOnDelete();
            $table->foreignId('diagnostic_dimension_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('scale'); // scale | single_choice | multiple_choice | true_false | text | ranking
            $table->text('content');
            $table->string('help_text')->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('reverse_scored')->default(false); // item invertido (Likert)
            $table->decimal('weight', 5, 2)->default(1);
            $table->integer('sort_order')->default(0);
            $table->json('settings')->nullable(); // min/max da escala, rótulos, etc.
            $table->timestamps();
            $table->index(['diagnostic_tool_id', 'sort_order']);
        });

        // Opções de resposta (escala / múltipla escolha)
        Schema::create('diagnostic_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostic_question_id')->constrained()->cascadeOnDelete();
            $table->string('content');
            $table->decimal('value', 8, 2)->default(0); // peso/pontuação da opção
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Aplicações (instância de um usuário respondendo uma ferramenta)
        Schema::create('diagnostic_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostic_tool_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tier')->nullable(); // start | strategic | transformation | continuous
            $table->string('status')->default('draft');
            $table->decimal('global_score', 5, 2)->nullable(); // AS Score normalizado 0-100
            $table->string('global_label')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'diagnostic_tool_id', 'status'], 'diag_assess_company_tool_status_idx');
            $table->index(['user_id', 'status']);
        });

        // Respostas individuais
        Schema::create('diagnostic_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostic_assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('diagnostic_question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('diagnostic_question_option_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('numeric_value', 8, 2)->nullable();
            $table->text('text_value')->nullable();
            $table->timestamps();
            $table->index(['diagnostic_assessment_id', 'diagnostic_question_id'], 'diag_answers_assessment_question_idx');
        });

        // Uploads de laudos externos (ex.: HumanGuide)
        Schema::create('diagnostic_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostic_assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('kind')->default('report'); // report | attachment
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->timestamps();
        });

        // Resultados (scores por dimensão e por índice componente)
        Schema::create('diagnostic_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostic_assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('component_tool_id')->nullable()->constrained('diagnostic_tools')->nullOnDelete();
            $table->foreignId('diagnostic_dimension_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('raw_score', 8, 2)->default(0);
            $table->decimal('max_score', 8, 2)->default(0);
            $table->decimal('normalized_score', 5, 2)->default(0); // 0-100
            $table->string('label')->nullable();
            $table->timestamps();
            $table->index('diagnostic_assessment_id');
        });

        // Laudo / análise (rascunho IA + versão revisada)
        Schema::create('diagnostic_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostic_assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ai_provider_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending'); // pending | ai_generated | in_review | approved | published
            $table->longText('ai_draft')->nullable();
            $table->longText('content')->nullable(); // versão final/revisada
            $table->string('archetype')->nullable();
            $table->json('swot')->nullable();
            $table->json('highlights')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnostic_reports');
        Schema::dropIfExists('diagnostic_results');
        Schema::dropIfExists('diagnostic_uploads');
        Schema::dropIfExists('diagnostic_answers');
        Schema::dropIfExists('diagnostic_assessments');
        Schema::dropIfExists('diagnostic_question_options');
        Schema::dropIfExists('diagnostic_questions');
        Schema::dropIfExists('diagnostic_dimensions');
        Schema::dropIfExists('diagnostic_tool_components');
        Schema::dropIfExists('diagnostic_tools');
        Schema::dropIfExists('ai_providers');
    }
};
