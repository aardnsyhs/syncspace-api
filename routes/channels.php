<?php

use App\Models\Board;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function (User $user, int $id) {
  return $user->id === $id;
});

// Board channel - only team members can subscribe
Broadcast::channel('board.{boardId}', function (User $user, int $boardId) {
  $board = Board::find($boardId);

  if (!$board) {
    return false;
  }

  // Check if user is a member of the team that owns this board
  return $board->team->hasMember($user);
});
