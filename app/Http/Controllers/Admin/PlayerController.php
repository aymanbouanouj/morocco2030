<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Players\StorePlayerRequest;
use App\Http\Requests\Admin\Players\UpdatePlayerRequest;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlayerController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(Player::class, 'player');
    }

    public function index(Request $request): View
    {
        $players = Player::query()
            ->with('team')
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('display_name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->when($request->integer('team_id'), fn ($query, $teamId) => $query->where('team_id', $teamId))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.players.index', [
            'players' => $players,
            'teams' => Team::query()->orderBy('name')->get(),
            'filters' => $request->only(['search', 'status', 'team_id']),
        ]);
    }

    public function create(): View
    {
        return view('admin.players.create', [
            'teams' => Team::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StorePlayerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_captain'] = $request->boolean('is_captain');

        $player = Player::query()->create($data);

        $this->recordAudit($request, $player, 'players.created', null, $player->toArray());

        return redirect()->route('admin.players.index')
            ->with('success', 'Player created successfully.');
    }

    public function edit(Player $player): View
    {
        return view('admin.players.edit', [
            'player' => $player,
            'teams' => Team::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdatePlayerRequest $request, Player $player): RedirectResponse
    {
        $original = $player->toArray();
        $data = $request->validated();
        $data['is_captain'] = $request->boolean('is_captain');
        $player->update($data);

        $this->recordAudit($request, $player, 'players.updated', $original, $player->fresh()->toArray());

        return redirect()->route('admin.players.index')
            ->with('success', 'Player updated successfully.');
    }

    public function destroy(Request $request, Player $player): RedirectResponse
    {
        $original = $player->toArray();
        $player->delete();

        $this->recordAudit($request, $player, 'players.deleted', $original);

        return redirect()->route('admin.players.index')
            ->with('success', 'Player archived successfully.');
    }
}
