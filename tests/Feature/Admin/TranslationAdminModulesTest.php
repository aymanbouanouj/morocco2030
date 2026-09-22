<?php

namespace Tests\Feature\Admin;

use App\Models\InterfaceTranslation;
use App\Models\Language;
use App\Support\PublicLocale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class TranslationAdminModulesTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_language_admin_routes_are_protected_and_permission_controlled(): void
    {
        $language = $this->makeLanguage('fr', ['name' => 'French', 'native_name' => 'Francais']);

        $this->get(route('admin.languages.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($this->makePublicUser())
            ->get(route('admin.languages.index'))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser())
            ->get(route('admin.languages.index'))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser(['languages.manage']))
            ->get(route('admin.languages.index'))
            ->assertOk()
            ->assertSee($language->name);
    }

    public function test_authorized_staff_can_update_safe_language_fields_without_changing_code(): void
    {
        $user = $this->makeStaffUser(['languages.manage']);
        $language = $this->makeLanguage('fr', [
            'name' => 'French',
            'native_name' => 'Francais',
            'direction' => 'ltr',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->actingAs($user)
            ->put(route('admin.languages.update', $language), [
                'name' => 'French Public',
                'native_name' => 'Francais Public',
                'code' => 'changed',
                'direction' => 'rtl',
                'is_active' => '1',
                'sort_order' => '8',
            ])
            ->assertRedirect(route('admin.languages.index'));

        $language->refresh();

        $this->assertSame('French Public', $language->name);
        $this->assertSame('Francais Public', $language->native_name);
        $this->assertSame('fr', $language->code);
        $this->assertSame('rtl', $language->direction);
        $this->assertTrue($language->is_active);
        $this->assertSame(8, $language->sort_order);
    }

    public function test_default_language_cannot_be_deactivated(): void
    {
        $user = $this->makeStaffUser(['languages.manage']);
        $language = $this->makeLanguage('en', [
            'name' => 'English',
            'native_name' => 'English',
            'is_default' => true,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->from(route('admin.languages.edit', $language))
            ->put(route('admin.languages.update', $language), [
                'name' => 'English',
                'native_name' => 'English',
                'direction' => 'ltr',
                'is_active' => '0',
                'sort_order' => '1',
            ])
            ->assertRedirect(route('admin.languages.edit', $language))
            ->assertSessionHasErrors('is_active');

        $this->assertTrue($language->fresh()->is_active);
    }

    public function test_interface_translation_routes_are_protected_and_permission_controlled(): void
    {
        $translation = $this->makeInterfaceTranslation();

        $this->get(route('admin.interface-translations.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($this->makePublicUser())
            ->get(route('admin.interface-translations.index'))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser())
            ->get(route('admin.interface-translations.index'))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser(['translations.manage']))
            ->get(route('admin.interface-translations.index'))
            ->assertOk()
            ->assertSee($translation->translation_key)
            ->assertSee($translation->value);
    }

    public function test_authorized_staff_can_update_translation_value_without_changing_key(): void
    {
        $user = $this->makeStaffUser(['translations.manage']);
        $translation = $this->makeInterfaceTranslation([
            'translation_key' => 'Home',
            'value' => 'Accueil',
        ]);

        $this->actingAs($user)
            ->put(route('admin.interface-translations.update', $translation), [
                'translation_key' => 'Changed Key',
                'value' => 'Accueil public',
            ])
            ->assertRedirect(route('admin.interface-translations.index', [
                'language_id' => $translation->language_id,
                'namespace' => $translation->namespace,
                'group_name' => $translation->group_name,
            ]));

        $translation->refresh();

        $this->assertSame('Home', $translation->translation_key);
        $this->assertSame('Accueil public', $translation->value);
    }

    public function test_missing_translation_report_renders_language_coverage_counts(): void
    {
        $user = $this->makeStaffUser(['translations.manage']);
        $english = $this->makeLanguage('en', ['name' => 'English', 'sort_order' => 1]);
        $french = $this->makeLanguage('fr', ['name' => 'French', 'sort_order' => 2]);

        InterfaceTranslation::query()->create([
            'language_id' => $english->id,
            'namespace' => 'public',
            'group_name' => 'json',
            'translation_key' => 'Home',
            'value' => 'Home',
        ]);

        InterfaceTranslation::query()->create([
            'language_id' => $english->id,
            'namespace' => 'public',
            'group_name' => 'json',
            'translation_key' => 'News',
            'value' => 'News',
        ]);

        InterfaceTranslation::query()->create([
            'language_id' => $french->id,
            'namespace' => 'public',
            'group_name' => 'json',
            'translation_key' => 'Home',
            'value' => 'Accueil',
        ]);

        $this->actingAs($user)
            ->get(route('admin.interface-translations.missing'))
            ->assertOk()
            ->assertSee('Missing Translation Report')
            ->assertSee('English')
            ->assertSee('French')
            ->assertSee('News');
    }

    public function test_public_database_translation_lookup_still_works_after_admin_modules_are_registered(): void
    {
        $french = $this->makeLanguage('fr', [
            'name' => 'French',
            'native_name' => 'Francais',
            'is_default' => true,
            'sort_order' => 1,
        ]);

        InterfaceTranslation::query()->create([
            'language_id' => $french->id,
            'namespace' => 'public',
            'group_name' => 'json',
            'translation_key' => 'Home',
            'value' => 'Accueil',
        ]);

        $this->withSession([PublicLocale::SESSION_KEY => 'fr'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Accueil');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeLanguage(string $code, array $attributes = []): Language
    {
        return Language::query()->create([
            'name' => $attributes['name'] ?? strtoupper($code),
            'native_name' => $attributes['native_name'] ?? strtoupper($code),
            'code' => $code,
            'locale' => $attributes['locale'] ?? $code.'_MA',
            'direction' => $attributes['direction'] ?? 'ltr',
            'is_default' => $attributes['is_default'] ?? false,
            'is_active' => $attributes['is_active'] ?? true,
            'sort_order' => $attributes['sort_order'] ?? 1,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeInterfaceTranslation(array $attributes = []): InterfaceTranslation
    {
        $language = $attributes['language'] ?? $this->makeLanguage('fr', [
            'name' => 'French',
            'native_name' => 'Francais',
        ]);

        return InterfaceTranslation::query()->create([
            'language_id' => $language->id,
            'namespace' => $attributes['namespace'] ?? 'public',
            'group_name' => $attributes['group_name'] ?? 'json',
            'translation_key' => $attributes['translation_key'] ?? 'Home',
            'value' => $attributes['value'] ?? 'Accueil',
        ]);
    }
}
