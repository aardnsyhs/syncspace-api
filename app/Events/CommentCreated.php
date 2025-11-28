<?php

namespace App\Events;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentCreated implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(
    public Comment $comment,
    public int $boardId,
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
      'comment' => new CommentResource($this->comment->load('user')),
      'card_id' => $this->cardId,
    ];
  }
}
