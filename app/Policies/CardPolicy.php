<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Card;
use App\Models\Column;
use App\Models\User;

class CardPolicy
{

  public function view(User $user, Card $card): bool
  {
    $team = $card->column->board->team;
    $role = $this->getUserRole($user, $team);
    return $role?->canViewBoards() ?? false;
  }

  public function create(User $user, Column $column): bool
  {
    $team = $column->board->team;
    $role = $this->getUserRole($user, $team);
    return $role?->canEditContent() ?? false;
  }

  public function update(User $user, Card $card): bool
  {
    $team = $card->column->board->team;
    $role = $this->getUserRole($user, $team);
    return $role?->canEditContent() ?? false;
  }

  public function delete(User $user, Card $card): bool
  {
    $team = $card->column->board->team;
    $role = $this->getUserRole($user, $team);
    return $role?->canEditContent() ?? false;
  }

  public function move(User $user, Card $card): bool
  {
    $team = $card->column->board->team;
    $role = $this->getUserRole($user, $team);
    return $role?->canEditContent() ?? false;
  }

  private function getUserRole(User $user, $team): ?TeamRole
  {
    $roleValue = $team->getMemberRole($user);
    return $roleValue ? TeamRole::tryFrom($roleValue) : null;
  }
}
