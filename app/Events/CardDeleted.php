<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CardDeleted implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(
    public int $boardId,
    public int $columnId,
    public int $cardId
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
      'card_id' => $this->cardId,
      'column_id' => $this->columnId,
    ];
  }
}
