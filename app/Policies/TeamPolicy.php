<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;

class TeamPolicy
{

  public function view(User $user, Team $team): bool
  {
    return $team->hasMember($user);
  }

  public function update(User $user, Team $team): bool
  {
    $role = $this->getUserRole($user, $team);
    return $role?->canManageTeam() ?? false;
  }

  public function delete(User $user, Team $team): bool
  {
    $role = $this->getUserRole($user, $team);
    return $role?->canDeleteTeam() ?? false;
  }

  public function manageMembers(User $user, Team $team): bool
  {
    $role = $this->getUserRole($user, $team);
    return $role?->canManageMembers() ?? false;
  }

  public function changeRoles(User $user, Team $team): bool
  {
    $role = $this->getUserRole($user, $team);
    return $role?->canManageMembers() ?? false;
  }

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
