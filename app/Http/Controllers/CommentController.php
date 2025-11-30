<?php

namespace App\Http\Controllers;

use App\Events\CommentCreated;
use App\Events\UserNotification;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Card;
use App\Models\Comment;
use App\Models\User;
use App\Services\ActivityService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
  public function __construct(
    private ActivityService $activityService,
    private NotificationService $notificationService
  ) {
  }

  public function index(Request $request, Card $card): AnonymousResourceCollection
  {
    $this->authorize('viewAny', [Comment::class, $card]);

    $comments = $card->comments()->with('user')->latest()->get();

    return CommentResource::collection($comments);
  }

  public function store(StoreCommentRequest $request, Card $card): JsonResponse
  {
    $this->authorize('create', [Comment::class, $card]);

    $comment = $card->comments()->create([
      'user_id' => $request->user()->id,
      'body' => $request->body,
    ]);

    $boardId = $card->column->board_id;

    $this->activityService->logCommentCreated($comment, $request->user());
    broadcast(new CommentCreated($comment, $boardId, $card->id))->toOthers();

    $commentPreview = mb_substr($comment->body, 0, 50);
    $this->notificationService->notifyNewComment($card, $request->user(), $commentPreview);

    if ($card->assignee_id && $card->assignee_id !== $request->user()->id) {
      broadcast(new UserNotification(
        userId: $card->assignee_id,
        type: 'comment_added',
        title: 'New comment on your card',
        message: "{$request->user()->name} commented on \"{$card->title}\"",
        data: [
          'card_id' => $card->id,
          'board_id' => $boardId,
          'comment_preview' => $commentPreview,
        ]
      ));
    }

    $this->notifyMentionedUsers($comment, $card, $request->user(), $boardId);

    return response()->json([
      'data' => new CommentResource($comment->load('user')),
    ], 201);
  }

  private function notifyMentionedUsers(Comment $comment, Card $card, User $commenter, int $boardId): void
  {
    
    $teamId = $card->column->board->team_id;
    $teamMembers = User::whereHas('teams', function ($query) use ($teamId) {
      $query->where('teams.id', $teamId);
    })->get();

    $notifiedUserIds = [];
    $commentBody = $comment->body;

    foreach ($teamMembers as $member) {
      
      if ($member->id === $commenter->id) {
        continue;
      }

      $pattern = '/@' . preg_quote($member->name, '/') . '\b/i';

      if (preg_match($pattern, $commentBody) && !in_array($member->id, $notifiedUserIds)) {
        $notifiedUserIds[] = $member->id;

        $this->notificationService->create(
          userId: $member->id,
          type: 'mention',
          title: 'You were mentioned in a comment',
          message: "{$commenter->name} mentioned you in \"{$card->title}\"",
          data: [
            'card_id' => $card->id,
            'board_id' => $boardId,
            'comment_id' => $comment->id,
            'comment_preview' => mb_substr($comment->body, 0, 50),
          ]
        );

        broadcast(new UserNotification(
          userId: $member->id,
          type: 'mention',
          title: 'You were mentioned in a comment',
          message: "{$commenter->name} mentioned you in \"{$card->title}\"",
          data: [
            'card_id' => $card->id,
            'board_id' => $boardId,
            'comment_id' => $comment->id,
            'comment_preview' => mb_substr($comment->body, 0, 50),
          ]
        ));
      }
    }
  }

  public function destroy(Request $request, Comment $comment): JsonResponse
  {
    $this->authorize('delete', $comment);

    $comment->delete();

    return response()->json(null, 204);
  }
}
