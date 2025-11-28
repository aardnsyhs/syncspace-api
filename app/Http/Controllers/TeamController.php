<?php

namespace App\Http\Controllers;

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
      ->withCount(['members', 'boards'])
      ->get();

    return TeamResource::collection($teams);
  }

  public function store(StoreTeamRequest $request): JsonResponse
  {
    $team = Team::create([
      'name' => $request->name,
      'slug' => Str::slug($request->name) . '-' . Str::random(5),
      'owner_id' => $request->user()->id,
    ]);

    // Add owner as member with owner role
    $team->members()->attach($request->user()->id, ['role' => 'owner']);

    return response()->json([
      'data' => new TeamResource($team->loadCount(['members', 'boards'])),
    ], 201);
  }

  public function show(Request $request, Team $team): JsonResponse
  {
    $this->authorizeTeamAccess($request, $team);

    return response()->json([
      'data' => new TeamResource($team->loadCount(['members', 'boards'])),
    ]);
  }

  public function update(UpdateTeamRequest $request, Team $team): JsonResponse
  {
    $this->authorizeTeamAdmin($request, $team);

    $team->update($request->validated());

    return response()->json([
      'data' => new TeamResource($team),
    ]);
  }

  public function destroy(Request $request, Team $team): JsonResponse
  {
    // Only owner can delete team
    if ($team->owner_id !== $request->user()->id) {
      abort(403, 'Only team owner can delete the team.');
    }

    $team->delete();

    return response()->json(null, 204);
  }

  private function authorizeTeamAccess(Request $request, Team $team): void
  {
    if (!$team->hasMember($request->user())) {
      abort(403, 'You are not a member of this team.');
    }
  }

  private function authorizeTeamAdmin(Request $request, Team $team): void
  {
    $role = $team->getMemberRole($request->user());
    if (!in_array($role, ['owner', 'admin'])) {
      abort(403, 'You do not have permission to manage this team.');
    }
  }
}
