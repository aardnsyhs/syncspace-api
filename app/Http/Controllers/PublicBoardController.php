<?php

namespace App\Http\Controllers;

use App\Events\BoardUpdated;
use App\Models\Board;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicBoardController extends Controller
{
  public function enable(Request $request, Board $board): JsonResponse
  {
    $this->authorize('update', $board);

    $board->enablePublicSharing();

    broadcast(new BoardUpdated($board))->toOthers();

    return response()->json([
      'data' => [
        'is_public' => true,
        'public_token' => $board->public_token,
        'public_url' => $board->public_url,
      ],
      'message' => 'Public sharing enabled',
    ]);
  }

  public function disable(Request $request, Board $board): JsonResponse
  {
    $this->authorize('update', $board);

    $board->disablePublicSharing();

    broadcast(new BoardUpdated($board))->toOthers();

    return response()->json([
      'data' => [
        'is_public' => false,
      ],
      'message' => 'Public sharing disabled',
    ]);
  }

  public function regenerate(Request $request, Board $board): JsonResponse
  {
    $this->authorize('update', $board);

    $board->regeneratePublicToken();

    broadcast(new BoardUpdated($board))->toOthers();

    return response()->json([
      'data' => [
        'public_token' => $board->public_token,
        'public_url' => $board->public_url,
      ],
      'message' => 'Public link regenerated. Old links are now invalid.',
    ]);
  }

  public function show(string $token): JsonResponse
  {
    $board = Board::where('public_token', $token)->first();

    if (!$board || !$board->isPublicLinkValid()) {
      return response()->json([
        'error' => 'Board not found or link has expired',
      ], 404);
    }

    $board->load([
      'columns' => fn($q) => $q->orderBy('position'),
      'columns.cards' => fn($q) => $q->orderBy('position'),
      'columns.cards.labels',
      'columns.cards.checklists.items',
      'labels',
    ]);

    return response()->json([
      'data' => $this->formatPublicBoard($board),
    ]);
  }

  private function formatPublicBoard(Board $board): array
  {
    return [
      'id' => $board->id,
      'name' => $board->name,
      'description' => $board->description,
      'color' => $board->color,
      'columns' => $board->columns->map(fn($column) => [
        'id' => $column->id,
        'name' => $column->name,
        'position' => $column->position,
        'wip_limit' => $column->wip_limit,
        'cards' => $column->cards->map(fn($card) => [
          'id' => $card->id,
          'title' => $card->title,
          'description' => $card->description,
          'position' => $card->position,
          'due_date' => $card->due_date,
          'labels' => $card->labels->map(fn($label) => [
            'id' => $label->id,
            'name' => $label->name,
            'color' => $label->color,
          ]),
          'checklist_progress' => $this->getChecklistProgress($card),

        ]),
      ]),
      'labels' => $board->labels->map(fn($label) => [
        'id' => $label->id,
        'name' => $label->name,
        'color' => $label->color,
      ]),
    ];
  }

  private function getChecklistProgress($card): ?array
  {
    if ($card->checklists->isEmpty()) {
      return null;
    }

    $total = 0;
    $completed = 0;

    foreach ($card->checklists as $checklist) {
      $total += $checklist->items->count();
      $completed += $checklist->items->where('is_completed', true)->count();
    }

    return [
      'total' => $total,
      'completed' => $completed,
      'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
    ];
  }
}
