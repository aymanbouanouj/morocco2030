<?php

namespace Tests\Feature\Public;

use App\Models\InterfaceTranslation;
use App\Models\Language;
use App\Models\Translation;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\PublicInterfaceTranslationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class PublicMultilingualCompletionTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_french_locale_renders_translated_interface_and_localized_content_fields(): void
    {
        $this->seed([
            LanguageSeeder::class,
            PublicInterfaceTranslationSeeder::class,
        ]);

        $city = $this->makeCity('Rabat', [
            'description' => 'English city description.',
        ]);

        $french = Language::query()->where('code', 'fr')->firstOrFail();

        Translation::query()->create([
            'translatable_type' => $city::class,
            'translatable_id' => $city->id,
            'language_id' => $french->id,
            'field' => 'description',
            'value' => 'Description francaise de la ville.',
        ]);

        $this->from(route('cities.show', $city->slug))
            ->get(route('language.switch', $french->code))
            ->assertRedirect(route('cities.show', $city->slug));

        $this->get(route('cities.show', $city->slug))
            ->assertOk()
            ->assertSee('lang="fr-MA"', false)
            ->assertSee('Accueil')
            ->assertSee('Recherche')
            ->assertSee('Description francaise de la ville.');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Se connecter')
            ->assertSee('Une connexion sécurisée');

        $this->get(route('search.index', ['q' => 'Rabat']))
            ->assertOk()
            ->assertSee('Recherche globale');

        $this->get(route('map.index'))
            ->assertOk()
            ->assertSee('Carte des villes et stades hôtes');
    }

    public function test_arabic_locale_keeps_public_pages_renderable_with_rtl_layout(): void
    {
        $this->seed([
            LanguageSeeder::class,
            PublicInterfaceTranslationSeeder::class,
        ]);

        $arabic = Language::query()->where('code', 'ar')->firstOrFail();

        $this->from(route('home'))
            ->get(route('language.switch', $arabic->code))
            ->assertRedirect(route('home'));

        $routes = [
            route('home'),
            route('login'),
            route('search.index'),
            route('map.index'),
            route('matches.index'),
            route('standings.index'),
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertOk();
        }

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('lang="ar-MA"', false)
            ->assertSee('dir="rtl"', false)
            ->assertSee('البحث')
            ->assertSee('الرئيسية');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('تسجيل الدخول')
            ->assertSee('تسجيل دخول موحد وآمن');
    }

    public function test_public_account_pages_render_with_translated_french_labels(): void
    {
        $this->seed([
            LanguageSeeder::class,
            PublicInterfaceTranslationSeeder::class,
        ]);

        $french = Language::query()->where('code', 'fr')->firstOrFail();
        $user = $this->makePublicUser([], [
            'preferred_locale' => $french->code,
        ]);

        $this->actingAs($user)
            ->withSession(['public_locale' => $french->code])
            ->get(route('account.index'))
            ->assertOk()
            ->assertSee('lang="fr-MA"', false)
            ->assertSee('Mon compte')
            ->assertSee('Votre espace personnel Maroc 2030');

        $this->actingAs($user)
            ->withSession(['public_locale' => $french->code])
            ->get(route('account.settings'))
            ->assertOk()
            ->assertSee('Paramètres')
            ->assertSee('Langue préférée');
    }

    public function test_expanded_public_languages_are_seeded_and_render_core_public_pages(): void
    {
        $this->seed([
            LanguageSeeder::class,
            PublicInterfaceTranslationSeeder::class,
        ]);

        $expectations = [
            'es' => ['locale' => 'es-ES', 'native' => 'Español', 'home' => 'Inicio', 'search' => 'Buscar', 'login' => 'Iniciar sesión', 'map' => 'Mapa de ciudades y estadios sede'],
            'it' => ['locale' => 'it-IT', 'native' => 'Italiano', 'home' => 'Home', 'search' => 'Cerca', 'login' => 'Accedi', 'map' => 'Mappa di città e stadi ospitanti'],
            'pt' => ['locale' => 'pt-PT', 'native' => 'Português', 'home' => 'Início', 'search' => 'Pesquisar', 'login' => 'Iniciar sessão', 'map' => 'Mapa de cidades e estádios-sede'],
            'zh' => ['locale' => 'zh-CN', 'native' => '中文', 'home' => '首页', 'search' => '搜索', 'login' => '登录', 'map' => '主办城市和球场地图'],
            'hi' => ['locale' => 'hi-IN', 'native' => 'हिन्दी', 'home' => 'होम', 'search' => 'खोज', 'login' => 'साइन इन', 'map' => 'मेज़बान शहर और स्टेडियम मानचित्र'],
            'de' => ['locale' => 'de-DE', 'native' => 'Deutsch', 'home' => 'Startseite', 'search' => 'Suche', 'login' => 'Anmelden', 'map' => 'Karte der Gastgeberstädte und Stadien'],
            'id' => ['locale' => 'id-ID', 'native' => 'Bahasa Indonesia', 'home' => 'Beranda', 'search' => 'Cari', 'login' => 'Masuk', 'map' => 'Peta kota dan stadion tuan rumah'],
        ];

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Español')
            ->assertSee('Italiano')
            ->assertSee('Português')
            ->assertSee('中文')
            ->assertSee('हिन्दी')
            ->assertSee('Deutsch')
            ->assertSee('Bahasa Indonesia');

        foreach ($expectations as $code => $labels) {
            $language = Language::query()->where('code', $code)->firstOrFail();

            $this->assertTrue($language->is_active);
            $this->assertSame('ltr', $language->direction);
            $this->assertSame($labels['native'], $language->native_name);

            $this->from(route('home'))
                ->get(route('language.switch', $code))
                ->assertRedirect(route('home'));

            $this->get(route('home'))
                ->assertOk()
                ->assertSee('lang="'.$labels['locale'].'"', false)
                ->assertSee('dir="ltr"', false)
                ->assertSee($labels['home'])
                ->assertSee($labels['search']);

            $this->get(route('login'))
                ->assertOk()
                ->assertSee($labels['login']);

            $this->get(route('search.index'))
                ->assertOk()
                ->assertSee($labels['search']);

            $this->get(route('map.index'))
                ->assertOk()
                ->assertSee($labels['map']);
        }
    }

    public function test_public_language_seeders_store_clean_utf8_values(): void
    {
        $this->seed([
            LanguageSeeder::class,
            PublicInterfaceTranslationSeeder::class,
        ]);

        $nativeNames = Language::query()
            ->whereIn('code', ['ar', 'fr', 'es', 'pt', 'zh', 'hi'])
            ->pluck('native_name', 'code');

        $this->assertSame('العربية', $nativeNames['ar']);
        $this->assertSame('Français', $nativeNames['fr']);
        $this->assertSame('Español', $nativeNames['es']);
        $this->assertSame('Português', $nativeNames['pt']);
        $this->assertSame('中文', $nativeNames['zh']);
        $this->assertSame('हिन्दी', $nativeNames['hi']);

        $samples = [
            ['ar', 'Home', 'الرئيسية'],
            ['ar', 'Search', 'البحث'],
            ['fr', 'Settings', 'Paramètres'],
            ['fr', 'Language switcher', 'Sélecteur de langue'],
            ['es', 'Sign In', 'Iniciar sesión'],
            ['pt', 'Home', 'Início'],
            ['zh', 'Home', '首页'],
            ['hi', 'Search', 'खोज'],
        ];

        foreach ($samples as [$code, $key, $expected]) {
            $language = Language::query()->where('code', $code)->firstOrFail();
            $value = InterfaceTranslation::query()
                ->where('language_id', $language->id)
                ->where('namespace', 'public')
                ->where('group_name', 'json')
                ->where('translation_key', $key)
                ->value('value');

            $this->assertSame($expected, $value);
            $this->assertDoesNotMatchRegularExpression('/(?:Ø|Ù|Ã|ä¸|à¤|à¥|FranÃ|EspaÃ|PortuguÃ)/u', $value);
        }
    }
}
