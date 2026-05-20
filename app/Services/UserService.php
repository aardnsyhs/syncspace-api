<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;

class UserService
{
  /**
   * Create a personal workspace for a new user.
   * The workspace name is configurable via config/workspace.php → default_workspace_name
   * or the APP_DEFAULT_WORKSPACE_NAME environment variable.
   */
  public function createPersonalWorkspace(User $user): Team
  {
    $team = Team::create([
      'name' => config('workspace.default_workspace_name', 'Personal Workspace'),
      'slug' => 'personal-' . Str::slug($user->name) . '-' . Str::random(5),
      'owner_id' => $user->id,
    ]);

    $team->members()->attach($user->id, ['role' => 'owner']);

    return $team;
  }

  /**
   * Check if user has any workspace
   */
  public function hasWorkspace(User $user): bool
  {
    return $user->teams()->exists();
  }

  /**
   * Create personal workspace if user doesn't have any
   */
  public function ensureHasWorkspace(User $user): ?Team
  {
    if (!$this->hasWorkspace($user)) {
      return $this->createPersonalWorkspace($user);
    }

    return null;
  }
}
