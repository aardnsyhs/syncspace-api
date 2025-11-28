<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Board;
use App\Models\Team;
use App\Models\User;

class BoardPolicy
{
  /**
   * Determine if user can view boards in a team
   */
  public function viewAny(User $user, Team $team): bool
  {
    $role = $this->getUserRole($user, $team);
    return $role?->canViewBoards() ?? false;
  }

  /**
   * Determine if user can view a specific board
   */
  public function view(User $user, Board $board): bool
  {
    $role = $this->getUserRole($user, $board->team);
    return $role?->canViewBoards() ?? false;
  }

  /**
   * Determine if user can create boards in a team
   */
  public function create(User $user, Team $team): bool
  {
    $role = $this->getUserRole($user, $team);
    return $role?->canManageBoards() ?? false;
  }

  /**
   * Determine if user can update a board
   */
  public function update(User $user, Board $board): bool
  {
    $role = $this->getUserRole($user, $board->team);
    return $role?->canManageBoards() ?? false;
  }

  /**
   * Determine if user can delete a board
   */
  public function delete(User $user, Board $board): bool
  {
    $role = $this->getUserRole($user, $board->team);
    return $role?->canManageBoards() ?? false;
  }

  /**
   * Determine if user can edit board content (columns, cards)
   */
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
