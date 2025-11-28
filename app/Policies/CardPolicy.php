<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Card;
use App\Models\Column;
use App\Models\User;

class CardPolicy
{
  /**
   * Determine if user can view a card
   */
  public function view(User $user, Card $card): bool
  {
    $team = $card->column->board->team;
    $role = $this->getUserRole($user, $team);
    return $role?->canViewBoards() ?? false;
  }

  /**
   * Determine if user can create cards in a column
   */
  public function create(User $user, Column $column): bool
  {
    $team = $column->board->team;
    $role = $this->getUserRole($user, $team);
    return $role?->canEditContent() ?? false;
  }

  /**
   * Determine if user can update a card
   */
  public function update(User $user, Card $card): bool
  {
    $team = $card->column->board->team;
    $role = $this->getUserRole($user, $team);
    return $role?->canEditContent() ?? false;
  }

  /**
   * Determine if user can delete a card
   */
  public function delete(User $user, Card $card): bool
  {
    $team = $card->column->board->team;
    $role = $this->getUserRole($user, $team);
    return $role?->canEditContent() ?? false;
  }

  /**
   * Determine if user can move a card
   */
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
