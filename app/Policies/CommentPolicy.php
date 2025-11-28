<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Card;
use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
  /**
   * Determine if user can view comments on a card
   */
  public function viewAny(User $user, Card $card): bool
  {
    $team = $card->column->board->team;
    $role = $this->getUserRole($user, $team);
    return $role?->canViewBoards() ?? false;
  }

  /**
   * Determine if user can create comments
   */
  public function create(User $user, Card $card): bool
  {
    $team = $card->column->board->team;
    $role = $this->getUserRole($user, $team);
    // Even viewers can comment
    return $role !== null;
  }

  /**
   * Determine if user can delete a comment
   */
  public function delete(User $user, Comment $comment): bool
  {
    // User can delete their own comments
    if ($comment->user_id === $user->id) {
      return true;
    }

    // Admins/Owners can delete any comment
    $team = $comment->card->column->board->team;
    $role = $this->getUserRole($user, $team);
    return $role?->canManageBoards() ?? false;
  }

  private function getUserRole(User $user, $team): ?TeamRole
  {
    $roleValue = $team->getMemberRole($user);
    return $roleValue ? TeamRole::tryFrom($roleValue) : null;
  }
}
