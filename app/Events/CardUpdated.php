<?php

namespace App\Events;

use App\Http\Resources\CardResource;
use App\Models\Card;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CardUpdated implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(
    public Card $card,
    public int $boardId
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
      'card' => new CardResource($this->card->load(['assignee', 'labels'])),
    ];
  }
}
