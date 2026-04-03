<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_path')->nullable();
            $table->string('primary_color', 7)->default('#2563EB');
            $table->string('secondary_color', 7)->default('#7C3AED');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('document', 20)->nullable()->comment('CNPJ');
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subscription_status')->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->integer('max_users')->default(25);
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
