<?php

namespace Tests\Feature\Admin;

use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class SponsorManagerPartnersTest extends TestCase
{
    use InteractsWithDemoAccessContract, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_sponsor_manager_can_create_and_archive_partners(): void
    {
        $user = $this->demoUser('sponsor.manager@morocco2030.test');

        $this->actingAs($user)
            ->get(route('admin.partners.index'))
            ->assertOk()
            ->assertSee('Create Partner');

        $this->actingAs($user)
            ->get(route('admin.partners.create'))
            ->assertOk()
            ->assertSee('Partner Details')
            ->assertSee('Partner logo')
            ->assertSee('name="logo"', false)
            ->assertSee('enctype="multipart/form-data"', false);

        $response = $this->actingAs($user)->post(route('admin.partners.store'), [
            'name' => 'Atlas Sponsor QA',
            'slug' => 'atlas-sponsor-qa',
            'category' => 'Official Partner',
            'tier' => 'Gold',
            'website_url' => 'https://sponsor.example',
            'contact_email' => 'partners@example.test',
            'contact_phone' => '+212 500 000 000',
            'description' => 'Local QA sponsor partner.',
            'status' => 'active',
        ]);

        $partner = Partner::query()->where('slug', 'atlas-sponsor-qa')->firstOrFail();

        $response->assertRedirect(route('admin.partners.index'));
        $this->assertDatabaseHas('partners', [
            'slug' => 'atlas-sponsor-qa',
            'status' => 'active',
            'deleted_at' => null,
        ]);

        $this->actingAs($user)
            ->get(route('admin.partners.index'))
            ->assertOk()
            ->assertSee('Atlas Sponsor QA')
            ->assertSee('partner-row-actions', false)
            ->assertSee(route('admin.partners.edit', $partner), false)
            ->assertDontSee('href="#"', false);

        $this->actingAs($user)
            ->delete(route('admin.partners.destroy', $partner))
            ->assertRedirect(route('admin.partners.index'));

        $this->assertSoftDeleted('partners', ['id' => $partner->id]);
    }

    public function test_sponsor_manager_sidebar_and_routes_stay_limited_to_partners_and_media(): void
    {
        $user = $this->demoUser('sponsor.manager@morocco2030.test');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertOk();

        $hrefs = $this->extractSidebarHrefs($response->getContent());

        $this->assertContains(route('admin.dashboard'), $hrefs);
        $this->assertContains(route('admin.partners.index'), $hrefs);
        $this->assertContains(route('admin.media-files.index'), $hrefs);

        foreach ([
            'admin.users.index',
            'admin.news.index',
            'admin.news-categories.index',
            'admin.matches.index',
            'admin.groups.index',
            'admin.teams.index',
            'admin.players.index',
            'admin.cities.index',
            'admin.stadiums.index',
            'admin.interface-translations.index',
            'admin.audit-logs.index',
            'admin.contact-messages.index',
            'admin.settings.index',
        ] as $routeName) {
            $this->assertNotContains(route($routeName), $hrefs, "Unexpected sidebar route: {$routeName}");
            $this->actingAs($user)->get(route($routeName))->assertForbidden();
        }

        $this->actingAs($user)->get(route('admin.partners.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.media-files.index'))->assertOk();
    }

    public function test_sponsor_manager_can_create_and_replace_partner_logo(): void
    {
        Storage::fake('public');

        $user = $this->demoUser('sponsor.manager@morocco2030.test');

        $this->actingAs($user)->post(route('admin.partners.store'), [
            'name' => 'Logo Sponsor QA',
            'slug' => 'logo-sponsor-qa',
            'category' => 'Official Partner',
            'tier' => 'Gold',
            'website_url' => 'https://logo-sponsor.example',
            'logo_alt' => 'Logo Sponsor QA mark',
            'logo' => UploadedFile::fake()->image('logo-sponsor.png', 240, 120),
            'status' => 'active',
        ])->assertRedirect(route('admin.partners.index'));

        $partner = Partner::query()->where('slug', 'logo-sponsor-qa')->firstOrFail();

        $this->assertNotNull($partner->logo_path);
        $this->assertStringStartsWith('partners/logos/', $partner->logo_path);
        Storage::disk('public')->assertExists($partner->logo_path);

        $this->actingAs($user)
            ->get(route('admin.partners.index'))
            ->assertOk()
            ->assertSee($partner->logoUrl(), false)
            ->assertSee('partner-row-actions', false)
            ->assertDontSee('href="#"', false);

        $this->actingAs($user)
            ->get(route('admin.partners.edit', $partner))
            ->assertOk()
            ->assertSee('Current logo')
            ->assertSee($partner->logoUrl(), false);

        $oldLogoPath = $partner->logo_path;

        $this->actingAs($user)->put(route('admin.partners.update', $partner), [
            'name' => 'Logo Sponsor QA',
            'slug' => 'logo-sponsor-qa',
            'category' => 'Official Partner',
            'tier' => 'Gold',
            'website_url' => 'https://logo-sponsor.example',
            'logo_alt' => 'Replacement Logo Sponsor QA mark',
            'logo' => UploadedFile::fake()->image('logo-sponsor-replacement.png', 240, 120),
            'status' => 'active',
        ])->assertRedirect(route('admin.partners.index'));

        $partner->refresh();

        $this->assertNotSame($oldLogoPath, $partner->logo_path);
        $this->assertSame('Replacement Logo Sponsor QA mark', $partner->logo_alt);
        Storage::disk('public')->assertMissing($oldLogoPath);
        Storage::disk('public')->assertExists($partner->logo_path);
    }

    public function test_partner_admin_search_and_status_filters_work_for_sponsor_manager(): void
    {
        $user = $this->demoUser('sponsor.manager@morocco2030.test');

        Partner::query()->create([
            'name' => 'Visible Sponsor Filter',
            'slug' => 'visible-sponsor-filter',
            'category' => 'Official Partner',
            'tier' => 'Gold',
            'status' => 'active',
        ]);

        Partner::query()->create([
            'name' => 'Inactive Sponsor Filter',
            'slug' => 'inactive-sponsor-filter',
            'category' => 'Official Partner',
            'tier' => 'Silver',
            'status' => 'inactive',
        ]);

        $this->actingAs($user)
            ->get(route('admin.partners.index', ['search' => 'Visible']))
            ->assertOk()
            ->assertSee('Visible Sponsor Filter')
            ->assertDontSee('Inactive Sponsor Filter');

        $this->actingAs($user)
            ->get(route('admin.partners.index', ['status' => 'inactive']))
            ->assertOk()
            ->assertSee('Inactive Sponsor Filter')
            ->assertDontSee('Visible Sponsor Filter');
    }
}
