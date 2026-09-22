<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            [
                'name' => 'Arabic',
                'native_name' => 'العربية',
                'code' => 'ar',
                'locale' => 'ar_MA',
                'direction' => 'rtl',
                'is_default' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'French',
                'native_name' => 'Français',
                'code' => 'fr',
                'locale' => 'fr_MA',
                'direction' => 'ltr',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'English',
                'native_name' => 'English',
                'code' => 'en',
                'locale' => 'en_US',
                'direction' => 'ltr',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Amazigh',
                'native_name' => 'Amazigh',
                'code' => 'zgh',
                'locale' => 'zgh_MA',
                'direction' => 'ltr',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Spanish',
                'native_name' => 'Español',
                'code' => 'es',
                'locale' => 'es_ES',
                'direction' => 'ltr',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Italian',
                'native_name' => 'Italiano',
                'code' => 'it',
                'locale' => 'it_IT',
                'direction' => 'ltr',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Portuguese',
                'native_name' => 'Português',
                'code' => 'pt',
                'locale' => 'pt_PT',
                'direction' => 'ltr',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Chinese',
                'native_name' => '中文',
                'code' => 'zh',
                'locale' => 'zh_CN',
                'direction' => 'ltr',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Hindi',
                'native_name' => 'हिन्दी',
                'code' => 'hi',
                'locale' => 'hi_IN',
                'direction' => 'ltr',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'German',
                'native_name' => 'Deutsch',
                'code' => 'de',
                'locale' => 'de_DE',
                'direction' => 'ltr',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Indonesian',
                'native_name' => 'Bahasa Indonesia',
                'code' => 'id',
                'locale' => 'id_ID',
                'direction' => 'ltr',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 11,
            ],
        ];

        foreach ($languages as $language) {
            Language::query()->updateOrCreate(
                ['code' => $language['code']],
                $language
            );
        }
    }
}
