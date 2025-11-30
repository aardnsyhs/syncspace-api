<?php

namespace App\Services;

use App\Events\UserNotification;
use App\Models\Card;
use App\Models\Notification;
use App\Models\User;

class NotificationService
{
  /**
   * Notify user when assigned to a card
   */
  public function notifyCardAssigned(Card $card, User $assignee, User $assigner): void
  {
    if ($assignee->id === $assigner->id) {
      return; // Don't notify if user assigned themselves
    }

    $data = [
      'card_id' => $card->id,
      'board_id' => $card->column->board_id,
      'assigner_id' => $assigner->id,
    ];

    Notification::create([
      'user_id' => $assignee->id,
      'type' => 'card_assigned',
      'title' => 'Card Assigned',
      'message' => "{$assigner->name} assigned you to '{$card->title}'",
      'data' => $data,
    ]);

    // Broadcast real-time notification
    broadcast(new UserNotification(
      userId: $assignee->id,
      type: 'card_assigned',
      title: 'Card Assigned',
      message: "{$assigner->name} assigned you to '{$card->title}'",
      data: $data
    ));
  }

  /**
   * Notify card owner/assignee about new comment
   */
  public function notifyNewComment(Card $card, User $commenter, string $commentPreview): void
  {
    $usersToNotify = collect();

    // Notify card assignee
    if ($card->assignee_id && $card->assignee_id !== $commenter->id) {
      $usersToNotify->push($card->assignee_id);
    }

    // Notify card creator if different
    if ($card->created_by && $card->created_by !== $commenter->id) {
      $usersToNotify->push($card->created_by);
    }

    $usersToNotify->unique()->each(function ($userId) use ($card, $commenter, $commentPreview) {
      $data = [
        'card_id' => $card->id,
        'board_id' => $card->column->board_id,
        'commenter_id' => $commenter->id,
      ];

      Notification::create([
        'user_id' => $userId,
        'type' => 'comment',
        'title' => 'New Comment',
        'message' => "{$commenter->name} commented on '{$card->title}': {$commentPreview}",
        'data' => $data,
      ]);

      // Broadcast real-time notification
      broadcast(new UserNotification(
        userId: $userId,
        type: 'comment',
        title: 'New Comment',
        message: "{$commenter->name} commented on '{$card->title}': {$commentPreview}",
        data: $data
      ));
    });
  }

  /**
   * Notify user about card due soon
   */
  public function notifyDueSoon(Card $card): void
  {
    if (!$card->assignee_id) {
      return;
    }

    Notification::create([
      'user_id' => $card->assignee_id,
      'type' => 'due_soon',
      'title' => 'Due Soon',
      'message' => "'{$card->title}' is due soon",
      'data' => [
        'card_id' => $card->id,
        'board_id' => $card->column->board_id,
        'due_date' => $card->due_date?->toISOString(),
      ],
    ]);
  }

  /**
   * Notify about card moved to different column
   */
  public function notifyCardMoved(Card $card, string $fromColumn, string $toColumn, User $mover): void
  {
    if (!$card->assignee_id || $card->assignee_id === $mover->id) {
      return;
    }

    $data = [
      'card_id' => $card->id,
      'board_id' => $card->column->board_id,
      'from_column' => $fromColumn,
      'to_column' => $toColumn,
    ];

    Notification::create([
      'user_id' => $card->assignee_id,
      'type' => 'card_moved',
      'title' => 'Card Moved',
      'message' => "{$mover->name} moved '{$card->title}' from {$fromColumn} to {$toColumn}",
      'data' => $data,
    ]);

    // Broadcast real-time notification
    broadcast(new UserNotification(
      userId: $card->assignee_id,
      type: 'card_moved',
      title: 'Card Moved',
      message: "{$mover->name} moved '{$card->title}' from {$fromColumn} to {$toColumn}",
      data: $data
    ));
  }

  /**
   * Notify user about mention in comment
   */
  public function notifyMention(User $mentionedUser, Card $card, User $mentioner): void
  {
    if ($mentionedUser->id === $mentioner->id) {
      return;
    }

    $data = [
      'card_id' => $card->id,
      'board_id' => $card->column->board_id,
      'mentioner_id' => $mentioner->id,
    ];

    Notification::create([
      'user_id' => $mentionedUser->id,
      'type' => 'mention',
      'title' => 'You were mentioned',
      'message' => "{$mentioner->name} mentioned you in '{$card->title}'",
      'data' => $data,
    ]);

    // Broadcast real-time notification
    broadcast(new UserNotification(
      userId: $mentionedUser->id,
      type: 'mention',
      title: 'You were mentioned',
      message: "{$mentioner->name} mentioned you in '{$card->title}'",
      data: $data
    ));
  }

  /**
   * Create a generic notification
   */
  public function create(
    int $userId,
    string $type,
    string $title,
    string $message,
    array $data = []
  ): Notification {
    return Notification::create([
      'user_id' => $userId,
      'type' => $type,
      'title' => $title,
      'message' => $message,
      'data' => $data,
    ]);
  }
}
