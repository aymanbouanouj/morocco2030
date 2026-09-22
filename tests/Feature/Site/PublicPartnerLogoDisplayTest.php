<?php

namespace Tests\Feature\Site;

use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicPartnerLogoDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_partners_page_displays_uploaded_partner_logo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('partners/logos/atlas-logo.png', 'logo');

        $partner = $this->makePartner([
            'name' => 'Atlas Logo Sponsor',
            'slug' => 'atlas-logo-sponsor',
            'logo_path' => 'partners/logos/atlas-logo.png',
            'logo_alt' => 'Atlas Logo Sponsor mark',
        ]);

        $response = $this->get(route('partners.index'))->assertOk();
        $localSection = $this->extractLocalPartnerSection($response->getContent());

        $this->assertStringContainsString($partner->logoUrl(), $localSection);
        $this->assertStringContainsString('Atlas Logo Sponsor mark', $localSection);
        $this->assertStringContainsString('data-local-partner-count="1"', $localSection);
        $this->assertStringNotContainsString('href="#"', $localSection);
    }

    public function test_homepage_partner_preview_displays_uploaded_partner_logo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('partners/logos/home-logo.png', 'logo');

        $partner = $this->makePartner([
            'name' => 'Homepage Logo Sponsor',
            'slug' => 'homepage-logo-sponsor',
            'logo_path' => 'partners/logos/home-logo.png',
            'logo_alt' => 'Homepage Logo Sponsor mark',
        ]);

        $response = $this->get(route('home'))->assertOk();
        $homePartnerSection = $this->extractHomePartnerSection($response->getContent());

        $this->assertStringContainsString($partner->logoUrl(), $homePartnerSection);
        $this->assertStringContainsString('Homepage Logo Sponsor mark', $homePartnerSection);
    }

    public function test_public_partners_page_uses_compact_fallback_without_logo(): void
    {
        $partner = $this->makePartner([
            'name' => 'Fallback Sponsor',
            'slug' => 'fallback-sponsor',
            'logo_path' => null,
            'logo_alt' => null,
        ]);

        $response = $this->get(route('partners.index'))->assertOk();
        $localSection = $this->extractLocalPartnerSection($response->getContent());

        $this->assertStringContainsString($partner->name, $localSection);
        $this->assertStringContainsString('partner-logo--text', $localSection);
        $this->assertStringContainsString('FA', $localSection);
    }

    public function test_public_partner_logo_pages_hide_inactive_and_archived_records(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('partners/logos/active-logo.png', 'logo');
        Storage::disk('public')->put('partners/logos/inactive-logo.png', 'logo');
        Storage::disk('public')->put('partners/logos/archived-logo.png', 'logo');

        $active = $this->makePartner([
            'name' => 'Active Logo Sponsor',
            'slug' => 'active-logo-sponsor',
            'logo_path' => 'partners/logos/active-logo.png',
        ]);
        $inactive = $this->makePartner([
            'name' => 'Inactive Logo Sponsor',
            'slug' => 'inactive-logo-sponsor',
            'status' => 'inactive',
            'logo_path' => 'partners/logos/inactive-logo.png',
        ]);
        $archived = $this->makePartner([
            'name' => 'Archived Logo Sponsor',
            'slug' => 'archived-logo-sponsor',
            'logo_path' => 'partners/logos/archived-logo.png',
        ]);
        $archived->delete();

        $this->get(route('partners.index'))
            ->assertOk()
            ->assertSee($active->name)
            ->assertDontSee($inactive->name)
            ->assertDontSee($archived->name);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($active->name)
            ->assertDontSee($inactive->name)
            ->assertDontSee($archived->name);
    }

    public function test_global_static_partners_are_separate_from_admin_partner_count(): void
    {
        $this->makePartner([
            'name' => 'Only Local Sponsor',
            'slug' => 'only-local-sponsor',
        ]);

        $response = $this->get(route('partners.index'))->assertOk();

        $this->assertStringContainsString('FIFA Global Partners', $response->getContent());
        $this->assertStringContainsString('data-local-partner-count="1"', $response->getContent());
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function makePartner(array $attributes = []): Partner
    {
        return Partner::query()->create([
            'name' => $attributes['name'] ?? 'Partner Logo QA',
            'slug' => $attributes['slug'] ?? 'partner-logo-qa',
            'category' => $attributes['category'] ?? 'Official Partner',
            'tier' => $attributes['tier'] ?? 'Gold',
            'website_url' => $attributes['website_url'] ?? 'https://partner.example',
            'logo_path' => $attributes['logo_path'] ?? null,
            'logo_alt' => $attributes['logo_alt'] ?? null,
            'status' => $attributes['status'] ?? 'active',
        ]);
    }

    private function extractLocalPartnerSection(string $html): string
    {
        if (preg_match('/<section class="entity-ref-section" aria-labelledby="partners-grid-title">.*?<\/section>/s', $html, $matches) !== 1) {
            $this->fail('Local partner section was not found.');
        }

        return $matches[0];
    }

    private function extractHomePartnerSection(string $html): string
    {
        if (preg_match('/<section class="home-final-partners home-final-card">.*?<\/section>/s', $html, $matches) !== 1) {
            $this->fail('Homepage partner preview section was not found.');
        }

        return $matches[0];
    }
}
