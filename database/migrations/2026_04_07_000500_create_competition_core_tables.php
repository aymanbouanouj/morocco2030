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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 16)->unique();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->restrictOnDelete();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('code', 16)->unique();
            $table->string('slug')->unique();
            $table->string('federation_name')->nullable();
            $table->unsignedSmallInteger('founded_year')->nullable();
            $table->string('coach_name')->nullable();
            $table->string('team_type', 32)->default('national')->index();
            $table->string('status', 32)->default('active')->index();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->restrictOnDelete();
            $table->string('display_name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('slug')->unique();
            $table->unsignedTinyInteger('shirt_number')->nullable();
            $table->string('position', 32)->index();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality_code', 2)->nullable()->index();
            $table->unsignedSmallInteger('height_cm')->nullable();
            $table->unsignedSmallInteger('weight_kg')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_captain')->default(false)->index();
            $table->string('status', 32)->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stadium_id')->nullable()->constrained('stadiums')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            $table->foreignId('home_team_id')->constrained('teams')->restrictOnDelete();
            $table->foreignId('away_team_id')->constrained('teams')->restrictOnDelete();
            $table->string('code', 32)->unique();
            $table->string('slug')->unique();
            $table->string('stage_type', 32)->index();
            $table->unsignedTinyInteger('round_number')->nullable()->index();
            $table->timestamp('match_date')->index();
            $table->string('timezone', 64)->nullable();
            $table->string('status', 32)->default('scheduled')->index();
            $table->unsignedInteger('attendance')->nullable();
            $table->unsignedTinyInteger('home_score')->nullable();
            $table->unsignedTinyInteger('away_score')->nullable();
            $table->unsignedTinyInteger('home_penalty_score')->nullable();
            $table->unsignedTinyInteger('away_penalty_score')->nullable();
            $table->boolean('extra_time_played')->default(false);
            $table->json('meta')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['home_team_id', 'away_team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
        Schema::dropIfExists('players');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('groups');
    }
};
