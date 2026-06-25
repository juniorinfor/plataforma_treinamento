<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('invite_token', 64)->nullable()->unique()->after('document');
            $table->boolean('allow_self_registration')->default(false)->after('invite_token');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['invite_token', 'allow_self_registration']);
        });
    }
};
