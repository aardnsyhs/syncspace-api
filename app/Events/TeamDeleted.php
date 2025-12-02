<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamDeleted implements ShouldBroadcastNow
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(
    public int $teamId
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
      'team_id' => $this->teamId,
    ];
  }

  public function broadcastAs(): string
  {
    return 'TeamDeleted';
  }
}
