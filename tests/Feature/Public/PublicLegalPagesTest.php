<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PublicLegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_privacy_notice_renders_without_authentication(): void
    {
        $this->assertTrue(Route::has('public.privacy'));

        $this->get(route('public.privacy'))
            ->assertOk()
            ->assertSee('Privacy Notice')
            ->assertSee('Visitor Analytics')
            ->assertSee('Raw IP addresses are not stored in visitor analytics')
            ->assertSee('Cookies And Sessions')
            ->assertSee('Contact Messages')
            ->assertSee('Administrative Audit Logs')
            ->assertSee('Retention Summary')
            ->assertSee('Law 09-08');
    }

    public function test_public_terms_page_renders_without_authentication(): void
    {
        $this->assertTrue(Route::has('public.terms'));

        $this->get(route('public.terms'))
            ->assertOk()
            ->assertSee('Terms Of Use')
            ->assertSee('Informational Platform')
            ->assertSee('Responsible Use')
            ->assertSee('No Official Affiliation Claim')
            ->assertSee(route('public.privacy'), false);
    }

    public function test_public_footer_contains_legal_links(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('public.privacy'), false)
            ->assertSee(route('public.terms'), false)
            ->assertSee('Privacy Policy')
            ->assertSee('Terms of Use');
    }
}
