<?php

namespace Tests\Feature\Admin;

use App\Models\Group;
use App\Models\MediaFile;
use App\Models\Partner;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class MediaReviewContextualAssetsTest extends TestCase
{
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_partner_logo_path_is_counted_and_listed_as_contextual_asset(): void
    {
        $partner = $this->makePartnerWithLogo('Adidas QA Partner', 'adidas QA logo');

        $html = $this->actingAs($this->demoUser('media.manager@morocco2030.test'))
            ->get(route('admin.media-files.index'))
            ->assertOk()
            ->assertSee('Partner logos', false)
            ->assertSee('1 total visual asset', false)
            ->assertSee('Partner Logo', false)
            ->assertSee($partner->name, false)
            ->assertSee($partner->logo_alt, false)
            ->getContent();

        $this->assertSummaryCount($html, 'Partner logos', 1);
        $this->assertStringContainsString(basename((string) $partner->logo_path), $html);
        $this->assertStringContainsString($partner->logoUrl(), $html);
    }

    public function test_search_finds_partner_logo_by_partner_name_or_alt_text(): void
    {
        $partner = $this->makePartnerWithLogo('Atlas Logo Sponsor', 'Atlas local sponsor mark');

        $this->actingAs($this->demoUser('media.manager@morocco2030.test'))
            ->get(route('admin.media-files.index', ['search' => 'Atlas Logo']))
            ->assertOk()
            ->assertSee($partner->name, false)
            ->assertSee('Partner Logo', false);

        $this->actingAs($this->demoUser('media.manager@morocco2030.test'))
            ->get(route('admin.media-files.index', ['search' => 'local sponsor mark']))
            ->assertOk()
            ->assertSee($partner->name, false)
            ->assertSee($partner->logo_alt, false);
    }

    public function test_type_filter_for_partner_logo_returns_only_partner_logo_assets(): void
    {
        $partner = $this->makePartnerWithLogo('Filtered Partner Logo', 'Filtered partner logo alt');
        $this->makeMediaFile('news', 'news-cover.webp');

        $response = $this->actingAs($this->demoUser('media.manager@morocco2030.test'))
            ->get(route('admin.media-files.index', ['type' => 'partner-logo']))
            ->assertOk()
            ->assertSee('Partner Logo', false)
            ->assertSee($partner->name, false)
            ->assertDontSee('news-cover.webp', false);

        $this->assertStringNotContainsString('href="#"', $response->getContent());
    }

    public function test_remote_football_data_crests_and_static_global_partners_are_not_counted(): void
    {
        $group = Group::query()->create([
            'name' => 'Group A',
            'code' => 'A',
            'sort_order' => 1,
        ]);

        Team::query()->create([
            'group_id' => $group->id,
            'name' => 'Remote Crest FC',
            'short_name' => 'RCF',
            'code' => 'RCF',
            'slug' => 'remote-crest-fc',
            'team_type' => 'national',
            'status' => 'active',
            'meta' => [
                'football_data' => [
                    'crest' => 'https://crests.example/remote.svg',
                ],
            ],
        ]);

        $html = $this->actingAs($this->demoUser('media.manager@morocco2030.test'))
            ->get(route('admin.media-files.index'))
            ->assertOk()
            ->assertSee('0 total visual asset', false)
            ->getContent();

        $this->assertSummaryCount($html, 'Partner logos', 0);
        $this->assertStringNotContainsString('remote.svg', $html);
        $this->assertStringNotContainsString('FIFA Global Partners', $html);
    }

    public function test_existing_media_files_and_partner_logos_are_both_included_in_total(): void
    {
        $this->makePartnerWithLogo('One Partner Logo', 'One partner logo alt');
        $this->makeMediaFile('news', 'news-cover.webp');

        $this->actingAs($this->demoUser('media.manager@morocco2030.test'))
            ->get(route('admin.media-files.index'))
            ->assertOk()
            ->assertSee('2 total visual asset', false)
            ->assertSee('One Partner Logo', false)
            ->assertSee('news-cover.webp', false);
    }

    private function makePartnerWithLogo(string $name, string $alt): Partner
    {
        Storage::fake('public');

        $path = UploadedFile::fake()
            ->image(str($name)->slug().'.png', 240, 120)
            ->store('partners/logos', 'public');

        return Partner::query()->create([
            'name' => $name,
            'slug' => str($name)->slug().'-'.Partner::query()->count(),
            'category' => 'Official Partner',
            'tier' => 'Gold',
            'website_url' => 'https://partner.example',
            'logo_path' => $path,
            'logo_alt' => $alt,
            'status' => 'active',
        ]);
    }

    private function makeMediaFile(string $category, string $filename): MediaFile
    {
        return MediaFile::query()->create([
            'disk' => 'public',
            'path' => 'media/'.$category.'/'.$filename,
            'filename' => $filename,
            'original_name' => $filename,
            'mime_type' => 'image/webp',
            'extension' => 'webp',
            'size_bytes' => 2048,
            'visibility' => 'public',
            'status' => 'active',
            'meta' => ['category' => $category],
        ]);
    }

    private function assertSummaryCount(string $html, string $label, int $count): void
    {
        $this->assertMatchesRegularExpression(
            '/'.preg_quote($label, '/').'<\/span>\s*<span class="admin-media-review-source__count">'.preg_quote((string) $count, '/').'<\/span>/',
            $html
        );
    }
}
