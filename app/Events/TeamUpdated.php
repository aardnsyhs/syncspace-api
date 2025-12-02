<?php

namespace App\Events;

use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamUpdated implements ShouldBroadcastNow
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(
    public Team $team
  ) {
  }

  public function broadcastOn(): array
  {
    return [
      new PrivateChannel("team.{$this->team->id}"),
    ];
  }

  public function broadcastWith(): array
  {
    return [
      'team' => new TeamResource($this->team),
    ];
  }

  public function broadcastAs(): string
  {
    return 'TeamUpdated';
  }
}
