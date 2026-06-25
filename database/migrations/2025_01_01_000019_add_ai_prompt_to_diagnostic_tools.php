<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagnostic_tools', function (Blueprint $table) {
            // Prompt de IA editável pelo Admin do Sistema, usado para gerar o
            // relatório do diagnóstico a partir das perguntas/resultados.
            $table->text('ai_prompt')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('diagnostic_tools', function (Blueprint $table) {
            $table->dropColumn('ai_prompt');
        });
    }
};
