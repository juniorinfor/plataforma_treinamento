<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('level_number')->unique();
            $table->integer('min_xp');
            $table->integer('max_xp')->nullable();
            $table->string('icon_path')->nullable();
            $table->string('color', 7)->nullable();
            $table->timestamps();
        });

        Schema::create('user_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->integer('total_xp')->default(0);
            $table->foreignId('current_level_id')->nullable()->constrained('levels')->nullOnDelete();
            $table->integer('weekly_xp')->default(0);
            $table->integer('monthly_xp')->default(0);
            $table->timestamp('weekly_reset_at')->nullable();
            $table->timestamp('monthly_reset_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'company_id']);
        });

        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->integer('xp_amount');
            $table->string('type');
            $table->string('description')->nullable();
            $table->nullableMorphs('reference');
            $table->timestamp('created_at')->nullable();
            $table->index(['user_id', 'company_id', 'created_at']);
        });

        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon_path')->nullable();
            $table->string('color', 7)->nullable();
            $table->string('category')->default('general');
            $table->string('criteria_type')->nullable();
            $table->json('criteria_config')->nullable();
            $table->integer('xp_reward')->default(0);
            $table->string('rarity')->default('common');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $table->timestamp('earned_at')->useCurrent();
            $table->boolean('notified')->default(false);
            $table->timestamp('created_at')->nullable();
            $table->unique(['user_id', 'badge_id']);
        });

        Schema::create('streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->integer('streak_freezes_remaining')->default(1);
            $table->timestamps();
            $table->unique(['user_id', 'company_id']);
        });

        Schema::create('streak_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streak_id')->constrained('streaks')->cascadeOnDelete();
            $table->date('activity_date');
            $table->integer('xp_earned')->default(0);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('daily');
            $table->string('criteria_type')->nullable();
            $table->json('criteria_config')->nullable();
            $table->integer('xp_reward')->default(30);
            $table->foreignId('badge_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            $table->integer('progress')->default(0);
            $table->integer('target')->default(1);
            $table->string('status')->default('in_progress');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'challenge_id']);
        });

        Schema::create('leaderboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('weekly');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamps();
        });

        Schema::create('leaderboard_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leaderboard_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('xp_total')->default(0);
            $table->integer('rank')->default(0);
            $table->timestamps();
            $table->unique(['leaderboard_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboard_entries');
        Schema::dropIfExists('leaderboards');
        Schema::dropIfExists('user_challenges');
        Schema::dropIfExists('challenges');
        Schema::dropIfExists('streak_history');
        Schema::dropIfExists('streaks');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('point_transactions');
        Schema::dropIfExists('user_points');
        Schema::dropIfExists('levels');
    }
};
