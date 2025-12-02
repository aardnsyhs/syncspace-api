<?php

namespace App\Http\Controllers;

use App\Events\TeamDeleted;
use App\Events\TeamUpdated;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class TeamController extends Controller
{
  public function index(Request $request): AnonymousResourceCollection
  {
    $teams = $request->user()
      ->teams()
      ->with([
        'boards' => function ($query) {
          $query->select('id', 'team_id', 'name', 'description', 'color', 'created_at')
            ->withCount('cards');
        }
      ])
      ->withCount(['members', 'boards'])
      ->get();

    foreach ($teams as $team) {
      foreach ($team->boards as $board) {
        $board->members_count = $team->members_count;
      }
    }

    return TeamResource::collection($teams);
  }

  public function store(StoreTeamRequest $request): JsonResponse
  {
    $team = Team::create([
      'name' => $request->name,
      'slug' => Str::slug($request->name) . '-' . Str::random(5),
      'owner_id' => $request->user()->id,
    ]);

    $team->members()->attach($request->user()->id, ['role' => 'owner']);

    return response()->json([
      'data' => new TeamResource($team->loadCount(['members', 'boards'])),
    ], 201);
  }

  public function show(Request $request, Team $team): JsonResponse
  {
    $this->authorize('view', $team);

    $team->load([
      'members' => function ($query) use ($request) {
        $query->where('user_id', $request->user()->id);
      }
    ]);

    return response()->json([
      'data' => new TeamResource($team->loadCount(['members', 'boards'])),
    ]);
  }

  public function update(UpdateTeamRequest $request, Team $team): JsonResponse
  {
    $this->authorize('update', $team);

    $team->update($request->validated());

    broadcast(new TeamUpdated($team))->toOthers();

    return response()->json([
      'data' => new TeamResource($team),
    ]);
  }

  public function destroy(Request $request, Team $team): JsonResponse
  {
    $this->authorize('delete', $team);

    $teamId = $team->id;

    $team->delete();

    broadcast(new TeamDeleted($teamId))->toOthers();

    return response()->json(null, 204);
  }
}
