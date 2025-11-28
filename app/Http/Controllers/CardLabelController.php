<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Label;
use App\Services\ActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CardLabelController extends Controller
{
  public function __construct(
    private ActivityService $activityService
  ) {
  }

  /**
   * POST /cards/{card}/labels
   * Attach one or more labels to a card
   */
  public function store(Request $request, Card $card): JsonResponse
  {
    $board = $card->column->board;
    $this->authorize('editContent', $board);

    $validated = $request->validate([
      'label_ids' => 'required|array',
      'label_ids.*' => 'integer|exists:labels,id',
    ]);

    // Verify labels belong to the same board
    $labels = Label::whereIn('id', $validated['label_ids'])
      ->where('board_id', $board->id)
      ->get();

    // Attach labels (sync without detaching existing)
    $card->labels()->syncWithoutDetaching($labels->pluck('id'));

    // Log activity for each new label
    $user = $request->user();
    foreach ($labels as $label) {
      $this->activityService->logLabelAdded($card, $user, $label);
    }

    return response()->json([
      'data' => $card->load('labels'),
    ]);
  }

  /**
   * DELETE /cards/{card}/labels/{label}
   * Detach a label from a card
   */
  public function destroy(Request $request, Card $card, Label $label): JsonResponse
  {
    $board = $card->column->board;
    $this->authorize('editContent', $board);

    $card->labels()->detach($label->id);

    // Log activity
    $this->activityService->logLabelRemoved($card, $request->user(), $label);

    return response()->json(null, 204);
  }
}
