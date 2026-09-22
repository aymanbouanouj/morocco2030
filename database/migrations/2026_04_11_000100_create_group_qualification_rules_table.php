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
        Schema::create('group_qualification_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->unsignedTinyInteger('qualifying_position')->index();
            $table->foreignId('target_match_id')->constrained('matches')->cascadeOnDelete();
            $table->string('team_slot', 16)->index();
            $table->string('label')->nullable();
            $table->foreignId('applied_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->dateTime('applied_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(
                ['group_id', 'qualifying_position', 'target_match_id', 'team_slot'],
                'group_qualification_rules_unique'
            );

            $table->unique(
                ['target_match_id', 'team_slot'],
                'group_qualification_rules_target_slot_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_qualification_rules');
    }
};
