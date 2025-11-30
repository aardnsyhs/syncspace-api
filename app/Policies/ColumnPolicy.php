<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Board;
use App\Models\Column;
use App\Models\User;

class ColumnPolicy
{

  public function create(User $user, Board $board): bool
  {
    $role = $this->getUserRole($user, $board->team);
    return $role?->canEditContent() ?? false;
  }

  public function update(User $user, Column $column): bool
  {
    $team = $column->board->team;
    $role = $this->getUserRole($user, $team);
    return $role?->canEditContent() ?? false;
  }

  public function delete(User $user, Column $column): bool
  {
    $team = $column->board->team;
    $role = $this->getUserRole($user, $team);
    return $role?->canEditContent() ?? false;
  }

  public function move(User $user, Column $column): bool
  {
    $team = $column->board->team;
    $role = $this->getUserRole($user, $team);
    return $role?->canEditContent() ?? false;
  }

  private function getUserRole(User $user, $team): ?TeamRole
  {
    $roleValue = $team->getMemberRole($user);
    return $roleValue ? TeamRole::tryFrom($roleValue) : null;
  }
}
