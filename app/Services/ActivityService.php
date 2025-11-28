<?php

namespace App\Services;

use App\Enums\ActivityType;
use App\Events\ActivityCreated;
use App\Models\Activity;
use App\Models\Board;
use App\Models\Card;
use App\Models\Column;
use App\Models\Comment;
use App\Models\User;

class ActivityService
{
  public function logCardCreated(Card $card, User $user): Activity
  {
    return $this->createActivity(
      boardId: $card->column->board_id,
      userId: $user->id,
      type: ActivityType::CREATED->value,
      data: [
        'entity' => 'card',
        'card_id' => $card->id,
        'card_title' => $card->title,
        'column_name' => $card->column->name,
      ],
      cardId: $card->id
    );
  }

  public function logCardUpdated(Card $card, User $user, array $changes): Activity
  {
    return $this->createActivity(
      boardId: $card->column->board_id,
      userId: $user->id,
      type: ActivityType::UPDATED->value,
      data: [
        'entity' => 'card',
        'card_id' => $card->id,
        'card_title' => $card->title,
        'changes' => $changes,
      ],
      cardId: $card->id
    );
  }

  public function logCardMoved(Card $card, User $user, string $fromColumn, string $toColumn): Activity
  {
    return $this->createActivity(
      boardId: $card->column->board_id,
      userId: $user->id,
      type: ActivityType::MOVED->value,
      data: [
        'entity' => 'card',
        'card_id' => $card->id,
        'card_title' => $card->title,
        'from_column' => $fromColumn,
        'to_column' => $toColumn,
      ],
      cardId: $card->id
    );
  }

  public function logCardDeleted(Board $board, User $user, string $cardTitle, string $columnName): Activity
  {
    return $this->createActivity(
      boardId: $board->id,
      userId: $user->id,
      type: ActivityType::UPDATED->value,
      data: [
        'entity' => 'card',
        'action' => 'deleted',
        'card_title' => $cardTitle,
        'column_name' => $columnName,
      ]
    );
  }

  public function logCardAssigned(Card $card, User $user, ?User $assignee): Activity
  {
    return $this->createActivity(
      boardId: $card->column->board_id,
      userId: $user->id,
      type: $assignee ? ActivityType::ASSIGNED->value : ActivityType::UNASSIGNED->value,
      data: [
        'entity' => 'card',
        'card_id' => $card->id,
        'card_title' => $card->title,
        'assignee_id' => $assignee?->id,
        'assignee_name' => $assignee?->name,
      ],
      cardId: $card->id
    );
  }

  public function logColumnCreated(Column $column, User $user): Activity
  {
    return $this->createActivity(
      boardId: $column->board_id,
      userId: $user->id,
      type: ActivityType::CREATED->value,
      data: [
        'entity' => 'column',
        'column_id' => $column->id,
        'column_name' => $column->name,
      ]
    );
  }

  public function logColumnUpdated(Column $column, User $user): Activity
  {
    return $this->createActivity(
      boardId: $column->board_id,
      userId: $user->id,
      type: ActivityType::UPDATED->value,
      data: [
        'entity' => 'column',
        'column_id' => $column->id,
        'column_name' => $column->name,
      ]
    );
  }

  public function logColumnDeleted(Board $board, User $user, string $columnName): Activity
  {
    return $this->createActivity(
      boardId: $board->id,
      userId: $user->id,
      type: ActivityType::UPDATED->value,
      data: [
        'entity' => 'column',
        'action' => 'deleted',
        'column_name' => $columnName,
      ]
    );
  }

  public function logCommentCreated(Comment $comment, User $user): Activity
  {
    $card = $comment->card;

    return $this->createActivity(
      boardId: $card->column->board_id,
      userId: $user->id,
      type: ActivityType::COMMENTED->value,
      data: [
        'entity' => 'comment',
        'card_id' => $card->id,
        'card_title' => $card->title,
        'comment_preview' => mb_substr($comment->body, 0, 100),
      ],
      cardId: $card->id
    );
  }

  private function createActivity(
    int $boardId,
    int $userId,
    string $type,
    array $data,
    ?int $cardId = null
  ): Activity {
    $activity = Activity::create([
      'board_id' => $boardId,
      'card_id' => $cardId,
      'user_id' => $userId,
      'type' => $type,
      'data' => $data,
      'created_at' => now(),
    ]);

    // Load relations for broadcast
    $activity->load('user');

    // Broadcast activity created event
    broadcast(new ActivityCreated($activity))->toOthers();

    return $activity;
  }
}
