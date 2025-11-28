<?php

namespace App\Http\Controllers;

use App\Http\Requests\Card\MoveCardRequest;
use App\Http\Requests\Card\StoreCardRequest;
use App\Http\Requests\Card\UpdateCardRequest;
use App\Http\Resources\CardResource;
use App\Models\Card;
use App\Models\Column;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CardController extends Controller
{
  public function store(StoreCardRequest $request, Column $column): JsonResponse
  {
    $this->authorizeColumnAccess($request, $column);

    // Get max position and add new card at the end
    $maxPosition = $column->cards()->max('position') ?? -1;

    $card = $column->cards()->create([
      ...$request->validated(),
      'position' => $maxPosition + 1,
    ]);

    return response()->json([
      'data' => new CardResource($card->load(['assignee', 'labels'])),
    ], 201);
  }

  public function show(Request $request, Card $card): JsonResponse
  {
    $this->authorizeCardAccess($request, $card);

    $card->load(['assignee', 'labels', 'comments.user']);

    return response()->json([
      'data' => new CardResource($card),
    ]);
  }

  public function update(UpdateCardRequest $request, Card $card): JsonResponse
  {
    $this->authorizeCardAccess($request, $card);

    $card->update($request->validated());

    return response()->json([
      'data' => new CardResource($card->load(['assignee', 'labels'])),
    ]);
  }

  public function destroy(Request $request, Card $card): JsonResponse
  {
    $this->authorizeCardAccess($request, $card);

    $column = $card->column;
    $position = $card->position;

    $card->delete();

    // Reorder remaining cards
    $column->cards()
      ->where('position', '>', $position)
      ->decrement('position');

    return response()->json(null, 204);
  }

  public function move(MoveCardRequest $request, Card $card): JsonResponse
  {
    $this->authorizeCardAccess($request, $card);

    $newColumnId = $request->column_id;
    $newPosition = $request->position;
    $oldColumnId = $card->column_id;
    $oldPosition = $card->position;

    DB::transaction(function () use ($card, $newColumnId, $newPosition, $oldColumnId, $oldPosition) {
      if ($newColumnId === $oldColumnId) {
        // Same column: reorder within column
        if ($newPosition > $oldPosition) {
          Card::where('column_id', $oldColumnId)
            ->whereBetween('position', [$oldPosition + 1, $newPosition])
            ->decrement('position');
        } else {
          Card::where('column_id', $oldColumnId)
            ->whereBetween('position', [$newPosition, $oldPosition - 1])
            ->increment('position');
        }
      } else {
        // Different column: remove from old, insert to new
        Card::where('column_id', $oldColumnId)
          ->where('position', '>', $oldPosition)
          ->decrement('position');

        Card::where('column_id', $newColumnId)
          ->where('position', '>=', $newPosition)
          ->increment('position');
      }

      $card->update([
        'column_id' => $newColumnId,
        'position' => $newPosition,
      ]);
    });

    return response()->json([
      'data' => new CardResource($card->fresh()->load(['assignee', 'labels'])),
    ]);
  }

  private function authorizeColumnAccess(Request $request, Column $column): void
  {
    if (!$column->board->team->hasMember($request->user())) {
      abort(403, 'You are not a member of this team.');
    }
  }

  private function authorizeCardAccess(Request $request, Card $card): void
  {
    if (!$card->column->board->team->hasMember($request->user())) {
      abort(403, 'You are not a member of this team.');
    }
  }
}
