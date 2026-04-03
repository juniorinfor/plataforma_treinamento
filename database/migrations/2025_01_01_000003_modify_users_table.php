<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->string('avatar_path')->nullable()->after('email');
            $table->string('role')->default('employee')->after('avatar_path');
            $table->boolean('is_active')->default(true)->after('role');
            $table->json('settings')->nullable()->after('is_active');
            $table->timestamp('last_login_at')->nullable()->after('settings');
            $table->index(['company_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex(['company_id', 'role']);
            $table->dropColumn(['company_id', 'avatar_path', 'role', 'is_active', 'settings', 'last_login_at']);
        });
    }
};
