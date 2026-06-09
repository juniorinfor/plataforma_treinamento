<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_challenge_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            // Chave do período: "2025-01-15" (daily) | "2025-W03" (weekly) | "2025-01" (monthly)
            $table->string('period_key', 20);
            $table->timestamp('completed_at')->useCurrent();

            // Cada usuário completa cada desafio apenas uma vez por período
            $table->unique(['user_id', 'challenge_id', 'period_key'], 'ucc_user_challenge_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_challenge_completions');
    }
};
