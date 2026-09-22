<?php

namespace Tests\Feature\Site;

use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPartnersVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_partners_index_renders_when_local_partner_table_is_empty(): void
    {
        $this->get(route('partners.index'))
            ->assertOk()
            ->assertSee('FIFA Global Partners')
            ->assertSee('No active partners are available yet.');
    }

    public function test_public_partners_index_only_shows_active_unarchived_local_partners(): void
    {
        $active = $this->makePartner('Active Sponsor QA', 'active-sponsor-qa', 'active');
        $inactive = $this->makePartner('Inactive Sponsor QA', 'inactive-sponsor-qa', 'inactive');
        $archived = $this->makePartner('Archived Sponsor QA', 'archived-sponsor-qa', 'active');
        $archived->delete();

        $this->get(route('partners.index'))
            ->assertOk()
            ->assertSee($active->name)
            ->assertDontSee($inactive->name)
            ->assertDontSee($archived->name);
    }

    public function test_homepage_partner_preview_only_uses_active_unarchived_local_partners(): void
    {
        $active = $this->makePartner('Homepage Sponsor QA', 'homepage-sponsor-qa', 'active');
        $inactive = $this->makePartner('Homepage Inactive Sponsor QA', 'homepage-inactive-sponsor-qa', 'inactive');
        $archived = $this->makePartner('Homepage Archived Sponsor QA', 'homepage-archived-sponsor-qa', 'active');
        $archived->delete();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Official Partners')
            ->assertSee($active->name)
            ->assertDontSee($inactive->name)
            ->assertDontSee($archived->name);
    }

    public function test_homepage_partner_preview_uses_public_tier_name_order_and_four_item_limit(): void
    {
        $this->makePartner('Zeta Sponsor QA', 'zeta-sponsor-qa', 'active', 'Silver');
        $this->makePartner('Alpha Sponsor QA', 'alpha-sponsor-qa', 'active', 'Gold');
        $this->makePartner('Beta Sponsor QA', 'beta-sponsor-qa', 'active', 'Gold');
        $this->makePartner('Delta Sponsor QA', 'delta-sponsor-qa', 'active', 'Silver');
        $this->makePartner('Omega Sponsor QA', 'omega-sponsor-qa', 'active', 'Bronze');

        $response = $this->get(route('home'))->assertOk();
        $content = $response->getContent();

        $this->assertStringContainsString('Alpha Sponsor QA', $content);
        $this->assertStringContainsString('Beta Sponsor QA', $content);
        $this->assertStringContainsString('Omega Sponsor QA', $content);
        $this->assertStringContainsString('Delta Sponsor QA', $content);
        $this->assertStringNotContainsString('Zeta Sponsor QA', $content);
        $this->assertLessThan(
            strpos($content, 'Beta Sponsor QA'),
            strpos($content, 'Alpha Sponsor QA')
        );
    }

    private function makePartner(
        string $name,
        string $slug,
        string $status = 'active',
        ?string $tier = 'Gold',
    ): Partner {
        return Partner::query()->create([
            'name' => $name,
            'slug' => $slug,
            'category' => 'Official Partner',
            'tier' => $tier,
            'status' => $status,
        ]);
    }
}
