<?php

namespace Tests\Feature\Admin;

use App\Models\MatchFixture;
use App\Models\MatchStatistic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class MatchStatisticFlowTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_authorized_staff_can_manage_match_statistics_and_duplicate_metrics_are_rejected(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);
        $group = $this->makeGroup('A');
        $homeTeam = $this->makeTeam($group, 'Morocco');
        $awayTeam = $this->makeTeam($group, 'Spain');

        $match = MatchFixture::query()->create([
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'M-STAT-01',
            'slug' => 'm-stat-01',
            'stage_type' => 'group',
            'match_date' => now()->addHour(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($user);

        $this->get(route('admin.matches.statistics.index', ['match' => $match]))->assertOk();
        $this->get(route('admin.matches.statistics.create', ['match' => $match]))->assertOk();

        $this->post(route('admin.matches.statistics.store', ['match' => $match]), [
            'team_id' => $homeTeam->id,
            'metric_key' => 'possession_percentage',
            'metric_value' => 61.5,
            'context' => 'full_time',
        ])->assertRedirect(route('admin.matches.statistics.index', ['match' => $match]));

        $statistic = MatchStatistic::query()->firstOrFail();

        $this->assertSame('61.5%', $statistic->display_value);

        $this->from(route('admin.matches.statistics.create', ['match' => $match]))
            ->post(route('admin.matches.statistics.store', ['match' => $match]), [
                'team_id' => $homeTeam->id,
                'metric_key' => 'possession_percentage',
                'metric_value' => 59,
                'context' => 'full_time',
            ])
            ->assertRedirect(route('admin.matches.statistics.create', ['match' => $match]))
            ->assertSessionHasErrors('metric_key');

        $this->get(route('admin.matches.statistics.edit', ['match' => $match, 'statistic' => $statistic]))->assertOk();

        $this->put(route('admin.matches.statistics.update', ['match' => $match, 'statistic' => $statistic]), [
            'team_id' => $homeTeam->id,
            'metric_key' => 'possession_percentage',
            'metric_value' => 58.25,
            'context' => 'full_time',
        ])->assertRedirect(route('admin.matches.statistics.index', ['match' => $match]));

        $this->assertDatabaseHas('match_statistics', [
            'id' => $statistic->id,
            'team_id' => $homeTeam->id,
            'metric_key' => 'possession_percentage',
            'context' => 'full_time',
            'display_value' => '58.25%',
        ]);

        $this->delete(route('admin.matches.statistics.destroy', ['match' => $match, 'statistic' => $statistic]))
            ->assertRedirect(route('admin.matches.statistics.index', ['match' => $match]));

        $this->assertDatabaseMissing('match_statistics', [
            'id' => $statistic->id,
        ]);
    }

    public function test_match_statistics_require_the_selected_team_to_be_part_of_the_fixture(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);
        $group = $this->makeGroup('A');
        $otherGroup = $this->makeGroup('B');
        $homeTeam = $this->makeTeam($group, 'Morocco');
        $awayTeam = $this->makeTeam($group, 'Spain');
        $otherTeam = $this->makeTeam($otherGroup, 'Brazil');

        $match = MatchFixture::query()->create([
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'M-STAT-02',
            'slug' => 'm-stat-02',
            'stage_type' => 'group',
            'match_date' => now()->addHour(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($user)
            ->from(route('admin.matches.statistics.create', ['match' => $match]))
            ->post(route('admin.matches.statistics.store', ['match' => $match]), [
                'team_id' => $otherTeam->id,
                'metric_key' => 'shots',
                'metric_value' => 9,
                'context' => 'full_time',
            ])
            ->assertRedirect(route('admin.matches.statistics.create', ['match' => $match]))
            ->assertSessionHasErrors('team_id');
    }
}
