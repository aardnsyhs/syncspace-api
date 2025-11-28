<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ColumnDeleted implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(
    public int $boardId,
    public int $columnId
  ) {
  }

  public function broadcastOn(): array
  {
    return [
      new PrivateChannel("board.{$this->boardId}"),
    ];
  }

  public function broadcastWith(): array
  {
    return [
      'column_id' => $this->columnId,
    ];
  }
}
