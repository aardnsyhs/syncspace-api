<?php

namespace App\Events;

use App\Http\Resources\BoardResource;
use App\Models\Board;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BoardUpdated implements ShouldBroadcastNow
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(
    public Board $board
  ) {
  }

  public function broadcastOn(): array
  {
    return [
      new PrivateChannel("board.{$this->board->id}"),
    ];
  }

  public function broadcastWith(): array
  {
    return [
      'board' => new BoardResource($this->board),
    ];
  }
}
