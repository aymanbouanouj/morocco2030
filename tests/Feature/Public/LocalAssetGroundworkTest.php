<?php

namespace Tests\Feature\Public;

use App\Support\AssetFallback;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class LocalAssetGroundworkTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_local_brand_and_placeholder_assets_exist(): void
    {
        $paths = [
            public_path('assets/brand/logo.svg'),
            public_path('assets/brand/favicon.svg'),
            public_path('assets/placeholders/team.svg'),
            public_path('assets/placeholders/player.svg'),
            public_path('assets/placeholders/city.svg'),
            public_path('assets/placeholders/stadium.svg'),
            public_path('assets/placeholders/news.svg'),
            public_path('assets/placeholders/partner.svg'),
            public_path('assets/placeholders/generic.svg'),
        ];

        foreach ($paths as $path) {
            $this->assertFileExists($path);
            $this->assertGreaterThan(100, filesize($path));
            $this->assertStringContainsString('<svg', file_get_contents($path));
        }
    }

    public function test_asset_fallback_helper_maps_known_and_unknown_types(): void
    {
        $this->assertSame('assets/placeholders/team.svg', AssetFallback::placeholderPath('team'));
        $this->assertSame('assets/placeholders/player.svg', AssetFallback::placeholderPath('player'));
        $this->assertSame('assets/placeholders/generic.svg', AssetFallback::placeholderPath('unknown'));
        $this->assertStringContainsString('assets/placeholders/stadium.svg', AssetFallback::placeholderUrl('stadium'));
        $this->assertContains('news', AssetFallback::knownTypes());
        $this->assertContains('generic', AssetFallback::knownTypes());
    }

    public function test_public_layout_references_official_favicon_assets(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('assets/brand/favicon.svg', false)
            ->assertDontSee('assets/brand/favicon/favicon.ico', false);
    }

    public function test_admin_layout_references_local_svg_favicon(): void
    {
        $this->actingAs($this->makeStaffUser())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('assets/brand/favicon.svg', false);
    }
}
