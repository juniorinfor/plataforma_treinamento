<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Planos de Ação ────────────────────────────────────────────
        Schema::create('diagnostic_action_plans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('diagnostic_assessment_id')
                  ->unique()
                  ->constrained('diagnostic_assessments')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // pending | in_progress | completed
            $table->string('status', 20)->default('pending');

            $table->unsignedSmallInteger('items_total')->default(0);
            $table->unsignedSmallInteger('items_done')->default(0);

            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // ── Itens do Plano ────────────────────────────────────────────
        Schema::create('diagnostic_action_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('diagnostic_action_plan_id')
                  ->constrained('diagnostic_action_plans')
                  ->cascadeOnDelete();

            // Resultado (índice/dimensão) ao qual este item pertence
            $table->foreignId('diagnostic_result_id')
                  ->nullable()
                  ->constrained('diagnostic_results')
                  ->nullOnDelete();

            // Curso recomendado (opcional)
            $table->foreignId('course_id')
                  ->nullable()
                  ->constrained('courses')
                  ->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            // action | course | reading | reflection
            $table->string('type', 20)->default('action');

            // pending | done
            $table->string('status', 20)->default('pending');

            $table->boolean('is_auto_generated')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnostic_action_items');
        Schema::dropIfExists('diagnostic_action_plans');
    }
};
