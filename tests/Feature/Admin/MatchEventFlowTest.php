<?php

namespace Tests\Feature\Admin;

use App\Models\MatchEvent;
use App\Models\MatchFixture;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class MatchEventFlowTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_authorized_staff_can_manage_match_events(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);
        $group = $this->makeGroup('A');
        $homeTeam = $this->makeTeam($group, 'Morocco');
        $awayTeam = $this->makeTeam($group, 'Spain');
        $homePlayer = $this->makePlayer($homeTeam, 'Youssef En-Nesyri');
        $relatedPlayer = $this->makePlayer($homeTeam, 'Abde Ezzalzouli');

        $match = MatchFixture::query()->create([
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'M-EVT-01',
            'slug' => 'm-evt-01',
            'stage_type' => 'group',
            'match_date' => now()->addHour(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($user);

        $this->get(route('admin.matches.events.index', ['match' => $match]))->assertOk();
        $this->get(route('admin.matches.events.create', ['match' => $match]))->assertOk();

        $this->post(route('admin.matches.events.store', ['match' => $match]), [
            'team_id' => $homeTeam->id,
            'player_id' => $homePlayer->id,
            'minute' => 18,
            'extra_minute' => 1,
            'period' => 'first_half',
            'event_type' => 'goal',
            'description' => 'Opened the scoring.',
        ])->assertRedirect(route('admin.matches.events.index', ['match' => $match]));

        $event = MatchEvent::query()->firstOrFail();

        $this->get(route('admin.matches.events.edit', ['match' => $match, 'event' => $event]))->assertOk();

        $this->put(route('admin.matches.events.update', ['match' => $match, 'event' => $event]), [
            'team_id' => $homeTeam->id,
            'player_id' => $homePlayer->id,
            'related_player_id' => $relatedPlayer->id,
            'minute' => 45,
            'extra_minute' => 2,
            'period' => 'first_half',
            'event_type' => 'substitution',
            'description' => 'Tactical change before half time.',
        ])->assertRedirect(route('admin.matches.events.index', ['match' => $match]));

        $this->assertDatabaseHas('match_events', [
            'id' => $event->id,
            'event_type' => 'substitution',
            'minute' => 45,
            'extra_minute' => 2,
            'related_player_id' => $relatedPlayer->id,
        ]);

        $this->delete(route('admin.matches.events.destroy', ['match' => $match, 'event' => $event]))
            ->assertRedirect(route('admin.matches.events.index', ['match' => $match]));

        $this->assertDatabaseMissing('match_events', [
            'id' => $event->id,
        ]);
    }

    public function test_match_event_validation_rejects_players_from_the_wrong_team(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);
        $group = $this->makeGroup('A');
        $otherGroup = $this->makeGroup('B');
        $homeTeam = $this->makeTeam($group, 'Morocco');
        $awayTeam = $this->makeTeam($group, 'Spain');
        $otherTeam = $this->makeTeam($otherGroup, 'Brazil');
        $otherPlayer = $this->makePlayer($otherTeam, 'Neymar Junior');

        $match = MatchFixture::query()->create([
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'M-EVT-02',
            'slug' => 'm-evt-02',
            'stage_type' => 'group',
            'match_date' => now()->addHour(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($user)
            ->from(route('admin.matches.events.create', ['match' => $match]))
            ->post(route('admin.matches.events.store', ['match' => $match]), [
                'team_id' => $homeTeam->id,
                'player_id' => $otherPlayer->id,
                'minute' => 12,
                'period' => 'first_half',
                'event_type' => 'goal',
            ])
            ->assertRedirect(route('admin.matches.events.create', ['match' => $match]))
            ->assertSessionHasErrors('player_id');
    }
}
