<?php

namespace App\Events;

use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityCreated implements ShouldBroadcastNow
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(
    public Activity $activity
  ) {
  }

  public function broadcastOn(): array
  {
    return [
      new PrivateChannel("board.{$this->activity->board_id}"),
    ];
  }

  public function broadcastWith(): array
  {
    return [
      'activity' => new ActivityResource($this->activity),
    ];
  }

  public function broadcastAs(): string
  {
    return 'ActivityCreated';
  }
}
