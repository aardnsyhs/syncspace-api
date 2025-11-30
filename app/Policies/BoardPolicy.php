<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Board;
use App\Models\Team;
use App\Models\User;

class BoardPolicy
{

  public function viewAny(User $user, Team $team): bool
  {
    $role = $this->getUserRole($user, $team);
    return $role?->canViewBoards() ?? false;
  }

  public function view(User $user, Board $board): bool
  {
    $role = $this->getUserRole($user, $board->team);
    return $role?->canViewBoards() ?? false;
  }

  public function create(User $user, Team $team): bool
  {
    $role = $this->getUserRole($user, $team);
    return $role?->canManageBoards() ?? false;
  }

  public function update(User $user, Board $board): bool
  {
    $role = $this->getUserRole($user, $board->team);
    return $role?->canManageBoards() ?? false;
  }

  public function delete(User $user, Board $board): bool
  {
    $role = $this->getUserRole($user, $board->team);
    return $role?->canManageBoards() ?? false;
  }

  public function editContent(User $user, Board $board): bool
  {
    $role = $this->getUserRole($user, $board->team);
    return $role?->canEditContent() ?? false;
  }

  private function getUserRole(User $user, Team $team): ?TeamRole
  {
    $roleValue = $team->getMemberRole($user);
    return $roleValue ? TeamRole::tryFrom($roleValue) : null;
  }
}
