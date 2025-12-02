<?php

namespace App\Events;

use App\Http\Resources\BoardResource;
use App\Models\Board;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BoardCreated implements ShouldBroadcastNow
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(
    public Board $board
  ) {
  }

  public function broadcastOn(): array
  {
    return [
      new PrivateChannel("team.{$this->board->team_id}"),
    ];
  }

  public function broadcastWith(): array
  {
    return [
      'board' => new BoardResource($this->board),
    ];
  }

  public function broadcastAs(): string
  {
    return 'BoardCreated';
  }
}
