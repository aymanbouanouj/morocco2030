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
        Schema::table('match_lineups', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('lineup_type');
            $table->index(['match_id', 'team_id', 'sort_order'], 'match_lineups_match_team_sort_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_lineups', function (Blueprint $table) {
            $table->dropIndex('match_lineups_match_team_sort_index');
            $table->dropColumn('sort_order');
        });
    }
};
