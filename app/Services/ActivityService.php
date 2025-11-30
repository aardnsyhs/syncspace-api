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

  public function logLabelAdded(Card $card, User $user, \App\Models\Label $label): Activity
  {
    return $this->createActivity(
      boardId: $card->column->board_id,
      userId: $user->id,
      type: ActivityType::LABEL_ADDED->value,
      data: [
        'entity' => 'label',
        'card_id' => $card->id,
        'card_title' => $card->title,
        'label_id' => $label->id,
        'label_name' => $label->name,
        'label_color' => $label->color,
      ],
      cardId: $card->id
    );
  }

  public function logLabelRemoved(Card $card, User $user, \App\Models\Label $label): Activity
  {
    return $this->createActivity(
      boardId: $card->column->board_id,
      userId: $user->id,
      type: ActivityType::LABEL_REMOVED->value,
      data: [
        'entity' => 'label',
        'card_id' => $card->id,
        'card_title' => $card->title,
        'label_id' => $label->id,
        'label_name' => $label->name,
        'label_color' => $label->color,
      ],
      cardId: $card->id
    );
  }

  public function logChecklistAdded(Card $card, User $user, \App\Models\Checklist $checklist): Activity
  {
    return $this->createActivity(
      boardId: $card->column->board_id,
      userId: $user->id,
      type: ActivityType::CHECKLIST_ADDED->value,
      data: [
        'entity' => 'checklist',
        'card_id' => $card->id,
        'card_title' => $card->title,
        'checklist_id' => $checklist->id,
        'checklist_title' => $checklist->title,
      ],
      cardId: $card->id
    );
  }

  public function logChecklistRemoved(Card $card, User $user, string $checklistTitle): Activity
  {
    return $this->createActivity(
      boardId: $card->column->board_id,
      userId: $user->id,
      type: ActivityType::CHECKLIST_REMOVED->value,
      data: [
        'entity' => 'checklist',
        'card_id' => $card->id,
        'card_title' => $card->title,
        'checklist_title' => $checklistTitle,
      ],
      cardId: $card->id
    );
  }

  public function logChecklistItemCompleted(Card $card, User $user, \App\Models\Checklist $checklist, \App\Models\ChecklistItem $item): Activity
  {
    return $this->createActivity(
      boardId: $card->column->board_id,
      userId: $user->id,
      type: ActivityType::CHECKLIST_ITEM_COMPLETED->value,
      data: [
        'entity' => 'checklist_item',
        'card_id' => $card->id,
        'card_title' => $card->title,
        'checklist_title' => $checklist->title,
        'item_title' => $item->title,
      ],
      cardId: $card->id
    );
  }

  public function logChecklistItemUncompleted(Card $card, User $user, \App\Models\Checklist $checklist, \App\Models\ChecklistItem $item): Activity
  {
    return $this->createActivity(
      boardId: $card->column->board_id,
      userId: $user->id,
      type: ActivityType::CHECKLIST_ITEM_UNCOMPLETED->value,
      data: [
        'entity' => 'checklist_item',
        'card_id' => $card->id,
        'card_title' => $card->title,
        'checklist_title' => $checklist->title,
        'item_title' => $item->title,
      ],
      cardId: $card->id
    );
  }

  public function logAttachmentAdded(Card $card, User $user, \App\Models\Attachment $attachment): Activity
  {
    return $this->createActivity(
      boardId: $card->column->board_id,
      userId: $user->id,
      type: ActivityType::ATTACHMENT_ADDED->value,
      data: [
        'entity' => 'attachment',
        'card_id' => $card->id,
        'card_title' => $card->title,
        'file_name' => $attachment->file_name,
        'is_external' => $attachment->is_external,
      ],
      cardId: $card->id
    );
  }

  public function logAttachmentRemoved(Card $card, User $user, string $fileName): Activity
  {
    return $this->createActivity(
      boardId: $card->column->board_id,
      userId: $user->id,
      type: ActivityType::ATTACHMENT_REMOVED->value,
      data: [
        'entity' => 'attachment',
        'card_id' => $card->id,
        'card_title' => $card->title,
        'file_name' => $fileName,
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

    $activity->load('user');

    broadcast(new ActivityCreated($activity))->toOthers();

    return $activity;
  }
}
