<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BoardDeleted implements ShouldBroadcastNow
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(
    public int $teamId,
    public int $boardId
  ) {
  }

  public function broadcastOn(): array
  {
    return [
      new PrivateChannel("team.{$this->teamId}"),
    ];
  }

  public function broadcastWith(): array
  {
    return [
      'board_id' => $this->boardId,
    ];
  }

  public function broadcastAs(): string
  {
    return 'BoardDeleted';
  }
}
