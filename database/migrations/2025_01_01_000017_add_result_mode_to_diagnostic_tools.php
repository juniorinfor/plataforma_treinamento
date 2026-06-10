<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagnostic_tools', function (Blueprint $table) {
            $table->string('result_mode')->default('individual')->after('input_source');
            $table->boolean('is_confidential')->default(false)->after('result_mode');
            $table->unsignedSmallInteger('min_responses')->default(5)->after('is_confidential');
        });

        // Aplica os modos definidos para as ferramentas já existentes.
        // Clima e NR1: agregados e confidenciais. Executive Mapping: individual.
        DB::table('diagnostic_tools')
            ->whereIn('code', ['IO', 'NR1'])
            ->update(['result_mode' => 'aggregated', 'is_confidential' => true, 'min_responses' => 5]);

        DB::table('diagnostic_tools')
            ->where('code', 'EXEC_MAP')
            ->update(['result_mode' => 'individual', 'is_confidential' => false]);
    }

    public function down(): void
    {
        Schema::table('diagnostic_tools', function (Blueprint $table) {
            $table->dropColumn(['result_mode', 'is_confidential', 'min_responses']);
        });
    }
};
