<?php

use App\Models\Board;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{userId}', function (User $user, int $userId) {
  return $user->id === $userId;
});

Broadcast::channel('board.{boardId}', function (User $user, int $boardId) {
  $board = Board::find($boardId);

  if (!$board) {
    return false;
  }

  return $board->team->hasMember($user);
});

Broadcast::channel('presence-board.{boardId}', function (User $user, int $boardId) {
  $board = Board::find($boardId);

  if (!$board || !$board->team->hasMember($user)) {
    return false;
  }

  return [
    'id' => $user->id,
    'name' => $user->name,
    'avatar_url' => $user->avatar_url,
  ];
});
