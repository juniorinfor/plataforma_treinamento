<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_content_id')->constrained()->cascadeOnDelete();
            $table->json('response');
            $table->timestamps();
            $table->unique(['user_id', 'lesson_content_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_interactions');
    }
};
