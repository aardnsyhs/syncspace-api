<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
  /**
   * Determine if user can view the team
   */
  public function view(User $user, Team $team): bool
  {
    return $team->hasMember($user);
  }

  /**
   * Determine if user can update team settings
   */
  public function update(User $user, Team $team): bool
  {
    $role = $this->getUserRole($user, $team);
    return $role?->canManageTeam() ?? false;
  }

  /**
   * Determine if user can delete the team
   */
  public function delete(User $user, Team $team): bool
  {
    $role = $this->getUserRole($user, $team);
    return $role?->canDeleteTeam() ?? false;
  }

  /**
   * Determine if user can manage team members
   */
  public function manageMembers(User $user, Team $team): bool
  {
    $role = $this->getUserRole($user, $team);
    return $role?->canManageMembers() ?? false;
  }

  /**
   * Determine if user can change member roles
   */
  public function changeRoles(User $user, Team $team): bool
  {
    $role = $this->getUserRole($user, $team);
    return $role?->canManageMembers() ?? false;
  }

  /**
   * Determine if user can transfer ownership
   */
  public function transferOwnership(User $user, Team $team): bool
  {
    return $team->owner_id === $user->id;
  }

  private function getUserRole(User $user, Team $team): ?TeamRole
  {
    $roleValue = $team->getMemberRole($user);
    return $roleValue ? TeamRole::tryFrom($roleValue) : null;
  }
}
