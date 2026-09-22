<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('cover_image_path')->nullable()->after('body');
            $table->json('gallery_image_paths')->nullable()->after('cover_image_path');
            $table->string('video_url', 2048)->nullable()->after('gallery_image_paths');
            $table->string('media_alt_text', 180)->nullable()->after('video_url');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn([
                'cover_image_path',
                'gallery_image_paths',
                'video_url',
                'media_alt_text',
            ]);
        });
    }
};
