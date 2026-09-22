<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! $this->usesMySqlFamilyDriver()) {
            return;
        }

        DB::statement('ALTER TABLE `matches` MODIFY `match_date` DATETIME NOT NULL');
        DB::statement('ALTER TABLE `visitor_analytics` MODIFY `event_at` DATETIME NOT NULL');
        DB::statement('ALTER TABLE `audit_logs` MODIFY `occurred_at` DATETIME NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! $this->usesMySqlFamilyDriver()) {
            return;
        }

        DB::statement('ALTER TABLE `matches` MODIFY `match_date` TIMESTAMP NOT NULL');
        DB::statement('ALTER TABLE `visitor_analytics` MODIFY `event_at` TIMESTAMP NOT NULL');
        DB::statement('ALTER TABLE `audit_logs` MODIFY `occurred_at` TIMESTAMP NOT NULL');
    }

    private function usesMySqlFamilyDriver(): bool
    {
        return in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);
    }
};
