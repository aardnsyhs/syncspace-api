<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamMemberController extends Controller
{
  public function index(Request $request, Team $team): AnonymousResourceCollection
  {
    $this->authorizeTeamAccess($request, $team);

    $members = $team->members()->get();

    return UserResource::collection($members);
  }

  public function store(Request $request, Team $team): JsonResponse
  {
    $this->authorizeTeamAdmin($request, $team);

    $request->validate([
      'email' => ['required', 'email', 'exists:users,email'],
      'role' => ['sometimes', 'in:admin,member'],
    ]);

    $user = User::where('email', $request->email)->firstOrFail();

    if ($team->hasMember($user)) {
      return response()->json([
        'message' => 'User is already a member of this team.',
      ], 422);
    }

    $team->members()->attach($user->id, [
      'role' => $request->input('role', 'member'),
    ]);

    return response()->json([
      'message' => 'Member added successfully.',
      'data' => new UserResource($user),
    ], 201);
  }

  public function destroy(Request $request, Team $team, User $user): JsonResponse
  {
    $this->authorizeTeamAdmin($request, $team);

    // Cannot remove owner
    if ($team->owner_id === $user->id) {
      return response()->json([
        'message' => 'Cannot remove team owner.',
      ], 422);
    }

    $team->members()->detach($user->id);

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
      abort(403, 'You do not have permission to manage team members.');
    }
  }
}
