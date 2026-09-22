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
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('native_name');
            $table->string('code', 10)->unique();
            $table->string('locale', 10)->unique();
            $table->string('direction', 3)->default('ltr')->index();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->morphs('translatable');
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('field', 100)->index();
            $table->longText('value')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(
                ['translatable_type', 'translatable_id', 'language_id', 'field'],
                'translations_lookup_unique'
            );
        });

        Schema::create('interface_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('namespace', 50)->default('public')->index();
            $table->string('group_name', 100)->default('messages')->index();
            $table->string('translation_key', 150);
            $table->longText('value');
            $table->timestamps();

            $table->unique(
                ['language_id', 'namespace', 'group_name', 'translation_key'],
                'interface_translations_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interface_translations');
        Schema::dropIfExists('translations');
        Schema::dropIfExists('languages');
    }
};
