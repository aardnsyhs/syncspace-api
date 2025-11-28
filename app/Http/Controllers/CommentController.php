<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Card;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
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

    return response()->json([
      'data' => new CommentResource($comment->load('user')),
    ], 201);
  }

  public function destroy(Request $request, Comment $comment): JsonResponse
  {
    // Only comment author or team admin can delete
    if ($comment->user_id !== $request->user()->id) {
      $team = $comment->card->column->board->team;
      $role = $team->getMemberRole($request->user());
      if (!in_array($role, ['owner', 'admin'])) {
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
