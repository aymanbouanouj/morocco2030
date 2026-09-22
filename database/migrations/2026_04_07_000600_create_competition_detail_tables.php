<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('match_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('player_id')->nullable()->constrained('players')->nullOnDelete();
            $table->foreignId('related_player_id')->nullable()->constrained('players')->nullOnDelete();
            $table->unsignedSmallInteger('minute')->default(0);
            $table->unsignedTinyInteger('extra_minute')->nullable();
            $table->string('period', 32)->nullable()->index();
            $table->string('event_type', 64)->index();
            $table->text('description')->nullable();
            $table->json('payload')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['match_id', 'minute', 'extra_minute']);
        });

        Schema::create('match_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('metric_key', 100)->index();
            $table->decimal('metric_value', 12, 4)->nullable();
            $table->string('display_value')->nullable();
            $table->string('context', 32)->default('full_time')->index();
            $table->timestamps();

            $table->unique(['match_id', 'team_id', 'metric_key', 'context'], 'match_statistics_unique');
        });

        Schema::create('match_lineups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->string('lineup_type', 32)->index();
            $table->string('position', 32)->nullable()->index();
            $table->unsignedTinyInteger('shirt_number')->nullable();
            $table->string('formation_slot', 32)->nullable();
            $table->boolean('is_captain')->default(false);
            $table->boolean('is_goalkeeper')->default(false);
            $table->unsignedSmallInteger('minute_in')->nullable();
            $table->unsignedSmallInteger('minute_out')->nullable();
            $table->timestamps();

            $table->unique(['match_id', 'team_id', 'player_id'], 'match_lineups_unique');
        });

        Schema::create('standings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->unsignedTinyInteger('position')->index();
            $table->unsignedTinyInteger('played')->default(0);
            $table->unsignedTinyInteger('won')->default(0);
            $table->unsignedTinyInteger('drawn')->default(0);
            $table->unsignedTinyInteger('lost')->default(0);
            $table->unsignedTinyInteger('goals_for')->default(0);
            $table->unsignedTinyInteger('goals_against')->default(0);
            $table->integer('goal_difference')->default(0);
            $table->unsignedTinyInteger('points')->default(0);
            $table->string('form', 16)->nullable();
            $table->integer('fair_play_points')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['group_id', 'team_id']);
            $table->unique(['group_id', 'position']);
        });

        Schema::create('knockout_progressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('target_match_id')->constrained('matches')->cascadeOnDelete();
            $table->string('progression_type', 16)->default('winner')->index();
            $table->string('team_slot', 16)->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(
                ['source_match_id', 'target_match_id', 'progression_type', 'team_slot'],
                'knockout_progressions_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knockout_progressions');
        Schema::dropIfExists('standings');
        Schema::dropIfExists('match_lineups');
        Schema::dropIfExists('match_statistics');
        Schema::dropIfExists('match_events');
    }
};
