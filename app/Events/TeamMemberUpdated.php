<?php

namespace App\Events;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamMemberUpdated implements ShouldBroadcastNow
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(
    public int $teamId,
    public User $member,
    public string $newRole
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
      'member' => new UserResource($this->member),
      'new_role' => $this->newRole,
    ];
  }

  public function broadcastAs(): string
  {
    return 'TeamMemberUpdated';
  }
}
