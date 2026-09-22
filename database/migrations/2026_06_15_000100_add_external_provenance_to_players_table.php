<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->string('external_provider')->nullable()->after('status');
            $table->string('external_id')->nullable()->after('external_provider');
            $table->json('external_payload')->nullable()->after('external_id');

            $table->index(['external_provider', 'external_id'], 'players_external_provider_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropIndex('players_external_provider_id_index');
            $table->dropColumn(['external_provider', 'external_id', 'external_payload']);
        });
    }
};
