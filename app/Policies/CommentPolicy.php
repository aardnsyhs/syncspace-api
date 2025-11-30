<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Card;
use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{

  public function viewAny(User $user, Card $card): bool
  {
    $team = $card->column->board->team;
    $role = $this->getUserRole($user, $team);
    return $role?->canViewBoards() ?? false;
  }

  public function create(User $user, Card $card): bool
  {
    $team = $card->column->board->team;
    $role = $this->getUserRole($user, $team);
    
    return $role !== null;
  }

  public function delete(User $user, Comment $comment): bool
  {
    
    if ($comment->user_id === $user->id) {
      return true;
    }

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
