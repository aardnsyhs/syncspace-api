<?php

use App\Models\Board;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// User private channel - for personal notifications
Broadcast::channel('user.{userId}', function (User $user, int $userId) {
  return $user->id === $userId;
});

// Board private channel - for board updates
Broadcast::channel('board.{boardId}', function (User $user, int $boardId) {
  $board = Board::find($boardId);

  if (!$board) {
    return false;
  }

  return $board->team->hasMember($user);
});

// Board presence channel - for online users
Broadcast::channel('presence-board.{boardId}', function (User $user, int $boardId) {
  $board = Board::find($boardId);

  if (!$board || !$board->team->hasMember($user)) {
    return false;
  }

  // Return user data for presence
  return [
    'id' => $user->id,
    'name' => $user->name,
    'avatar_url' => $user->avatar_url,
  ];
});
