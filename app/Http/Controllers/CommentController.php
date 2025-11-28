<?php

namespace App\Http\Controllers;

use App\Events\CommentCreated;
use App\Events\UserNotification;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Card;
use App\Models\Comment;
use App\Services\ActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
  public function __construct(
    private ActivityService $activityService
  ) {
  }

  public function index(Request $request, Card $card): AnonymousResourceCollection
  {
    $this->authorizeCardAccess($request, $card);

    $comments = $card->comments()->with('user')->latest()->get();

    return CommentResource::collection($comments);
  }

  public function store(StoreCommentRequest $request, Card $card): JsonResponse
  {
    $this->authorizeCardAccess($request, $card);

    $comment = $card->comments()->create([
      'user_id' => $request->user()->id,
      'body' => $request->body,
    ]);

    $boardId = $card->column->board_id;

    // Log activity
    $this->activityService->logCommentCreated($comment, $request->user());

    // Broadcast comment event to board
    broadcast(new CommentCreated($comment, $boardId, $card->id))->toOthers();

    // Send notification to card creator if different from commenter
    if ($card->assignee_id && $card->assignee_id !== $request->user()->id) {
      broadcast(new UserNotification(
        userId: $card->assignee_id,
        type: 'comment_added',
        title: 'New comment on your card',
        message: "{$request->user()->name} commented on \"{$card->title}\"",
        data: [
          'card_id' => $card->id,
          'board_id' => $boardId,
          'comment_preview' => mb_substr($comment->body, 0, 50),
        ]
      ));
    }

    return response()->json([
      'data' => new CommentResource($comment->load('user')),
    ], 201);
  }

  public function destroy(Request $request, Comment $comment): JsonResponse
  {
    if ($comment->user_id !== $request->user()->id) {
      $team = $comment->card->column->board->team;
      $role = $team->getMemberRole($request->user());
      if (!\in_array($role, ['owner', 'admin'])) {
        abort(403, 'You can only delete your own comments.');
      }
    }

    $comment->delete();

    return response()->json(null, 204);
  }

  private function authorizeCardAccess(Request $request, Card $card): void
  {
    if (!$card->column->board->team->hasMember($request->user())) {
      abort(403, 'You are not a member of this team.');
    }
  }
}
