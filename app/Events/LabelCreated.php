<?php

namespace App\Events;

use App\Models\Label;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LabelCreated implements ShouldBroadcastNow
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(
    public Label $label,
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
      'label' => [
        'id' => $this->label->id,
        'board_id' => $this->label->board_id,
        'name' => $this->label->name,
        'color' => $this->label->color,
      ],
    ];
  }

  public function broadcastAs(): string
  {
    return 'LabelCreated';
  }
}
