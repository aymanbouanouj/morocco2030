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
        Schema::create('visitor_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->string('path')->index();
            $table->string('route_name')->nullable()->index();
            $table->string('referrer')->nullable();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('country_code', 2)->nullable()->index();
            $table->string('city_name')->nullable()->index();
            $table->string('language_code', 10)->nullable()->index();
            $table->string('device_type', 32)->nullable()->index();
            $table->string('event_type', 32)->default('page_view')->index();
            $table->timestamp('event_at')->index();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['path', 'event_at']);
        });

        Schema::create('sports_analytics_snapshots', function (Blueprint $table) {
            $table->id();
            $table->morphs('analyzable');
            $table->string('metric_key', 100)->index();
            $table->date('snapshot_date')->index();
            $table->decimal('metric_value', 14, 4)->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(
                ['analyzable_type', 'analyzable_id', 'metric_key', 'snapshot_date'],
                'sports_analytics_snapshots_unique'
            );
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->morphs('auditable');
            $table->string('action', 64)->index();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('route_name')->nullable()->index();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group_name', 100)->index();
            $table->string('setting_key', 100);
            $table->text('value')->nullable();
            $table->string('type', 32)->default('string')->index();
            $table->boolean('is_public')->default(false)->index();
            $table->boolean('autoload')->default(true)->index();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['group_name', 'setting_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('sports_analytics_snapshots');
        Schema::dropIfExists('visitor_analytics');
    }
};
