<?php

namespace Tests\Feature\Release;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class FooterOwnershipTest extends TestCase
{
    use BuildsAdminTestData;
    use RefreshDatabase;

    public function test_public_footer_contains_ayman_bounaouj_on_home(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Ayman Bounaouj', false)
            ->assertDontSee('href="#"', false);
    }

    public function test_public_footer_contains_ayman_bounaouj_on_core_public_pages(): void
    {
        $publicUser = $this->makePublicUser();

        foreach ([
            route('teams.index'),
            route('matches.index'),
            route('partners.index'),
        ] as $route) {
            $this->get($route)
                ->assertOk()
                ->assertSee('Ayman Bounaouj', false)
                ->assertDontSee('href="#"', false);
        }

        $this->actingAs($publicUser)
            ->get(route('account.index'))
            ->assertOk()
            ->assertSee('Ayman Bounaouj', false)
            ->assertDontSee('href="#"', false);
    }
}
