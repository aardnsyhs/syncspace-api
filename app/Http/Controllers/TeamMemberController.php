<?php

namespace App\Http\Controllers;

use App\Enums\TeamRole;
use App\Events\TeamMemberAdded;
use App\Events\TeamMemberRemoved;
use App\Events\TeamMemberUpdated;
use App\Http\Resources\UserResource;
use App\Models\Team;
use App\Models\User;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class TeamMemberController extends Controller
{
  public function index(Request $request, Team $team): AnonymousResourceCollection
  {
    $this->authorize('view', $team);

    $members = $team->members()->get();

    return UserResource::collection($members)->additional([
      'meta' => [
        'current_user_role' => $team->getMemberRole($request->user()),
      ],
    ]);
  }

  public function store(Request $request, Team $team): JsonResponse
  {
    $this->authorize('manageMembers', $team);

    $validated = $request->validate([
      'email' => ['required', 'email', 'exists:users,email'],
      'role' => ['sometimes', Rule::in(['admin', 'member', 'viewer'])],
    ]);

    $user = User::where('email', $validated['email'])->firstOrFail();

    if ($team->hasMember($user)) {
      return response()->json([
        'message' => 'User is already a member of this team.',
      ], 422);
    }

    $requestedRole = $validated['role'] ?? 'member';
    $currentUserRole = TeamRole::tryFrom($team->getMemberRole($request->user()));

    if ($requestedRole === 'admin' && $currentUserRole !== TeamRole::OWNER) {
      return response()->json([
        'message' => 'Only team owner can add admins.',
      ], 403);
    }

    $team->members()->attach($user->id, [
      'role' => $requestedRole,
    ]);

    // Send invitation email
    if ($user->email_notifications) {
      $user->notify(new TeamInvitationNotification($team, $request->user(), $requestedRole));
    }

    broadcast(new TeamMemberAdded($team->id, $user, $requestedRole))->toOthers();

    return response()->json([
      'message' => 'Member added successfully. An invitation email has been sent.',
      'data' => new UserResource($user),
    ], 201);
  }

  public function update(Request $request, Team $team, User $user): JsonResponse
  {
    $this->authorize('changeRoles', $team);

    $validated = $request->validate([
      'role' => ['required', Rule::in(TeamRole::values())],
    ]);

    $newRole = TeamRole::from($validated['role']);
    $currentUserRole = TeamRole::tryFrom($team->getMemberRole($request->user()));
    $targetUserRole = TeamRole::tryFrom($team->getMemberRole($user));

    if (!$targetUserRole) {
      return response()->json([
        'message' => 'User is not a member of this team.',
      ], 404);
    }

    if ($user->id === $request->user()->id) {
      return response()->json([
        'message' => 'You cannot change your own role.',
      ], 422);
    }

    if ($newRole === TeamRole::OWNER || $targetUserRole === TeamRole::OWNER) {
      if ($currentUserRole !== TeamRole::OWNER) {
        return response()->json([
          'message' => 'Only team owner can transfer ownership.',
        ], 403);
      }

      if ($newRole === TeamRole::OWNER) {

        $team->members()->updateExistingPivot($request->user()->id, ['role' => 'admin']);
        $team->update(['owner_id' => $user->id]);
      }
    }

    if ($currentUserRole === TeamRole::ADMIN && $targetUserRole === TeamRole::ADMIN) {
      return response()->json([
        'message' => 'Admins cannot change other admin roles.',
      ], 403);
    }

    $team->members()->updateExistingPivot($user->id, ['role' => $newRole->value]);

    broadcast(new TeamMemberUpdated($team->id, $user, $newRole->value))->toOthers();

    return response()->json([
      'message' => 'Role updated successfully.',
      'data' => new UserResource($user->fresh()),
    ]);
  }

  public function destroy(Request $request, Team $team, User $user): JsonResponse
  {
    $this->authorize('manageMembers', $team);

    $currentUserRole = TeamRole::tryFrom($team->getMemberRole($request->user()));
    $targetUserRole = TeamRole::tryFrom($team->getMemberRole($user));

    if (!$targetUserRole) {
      return response()->json([
        'message' => 'User is not a member of this team.',
      ], 404);
    }

    if ($targetUserRole === TeamRole::OWNER) {
      return response()->json([
        'message' => 'Cannot remove team owner. Transfer ownership first.',
      ], 422);
    }

    if ($currentUserRole === TeamRole::ADMIN && $targetUserRole === TeamRole::ADMIN) {
      return response()->json([
        'message' => 'Admins cannot remove other admins.',
      ], 403);
    }

    if ($user->id === $request->user()->id) {
      $team->members()->detach($user->id);
      broadcast(new TeamMemberRemoved($team->id, $user->id))->toOthers();
      return response()->json(null, 204);
    }

    $team->members()->detach($user->id);

    broadcast(new TeamMemberRemoved($team->id, $user->id))->toOthers();

    return response()->json(null, 204);
  }
}
