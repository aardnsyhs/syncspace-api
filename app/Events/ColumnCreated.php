<?php

namespace App\Events;

use App\Http\Resources\ColumnResource;
use App\Models\Column;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ColumnCreated implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(
    public Column $column
  ) {
  }

  public function broadcastOn(): array
  {
    return [
      new PrivateChannel("board.{$this->column->board_id}"),
    ];
  }

  public function broadcastWith(): array
  {
    return [
      'column' => new ColumnResource($this->column),
    ];
  }
}
