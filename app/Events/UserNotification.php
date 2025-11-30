<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserNotification implements ShouldBroadcastNow
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(
    public int $userId,
    public string $type,
    public string $title,
    public string $message,
    public array $data = []
  ) {
  }

  public function broadcastOn(): array
  {
    return [
      new PrivateChannel("user.{$this->userId}"),
    ];
  }

  public function broadcastWith(): array
  {
    return [
      'type' => $this->type,
      'title' => $this->title,
      'message' => $this->message,
      'data' => $this->data,
      'created_at' => now()->toISOString(),
    ];
  }

  public function broadcastAs(): string
  {
    return 'UserNotification';
  }
}
