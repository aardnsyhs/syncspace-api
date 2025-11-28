<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\ChecklistItem;
use App\Services\ActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChecklistItemController extends Controller
{
  public function __construct(
    private ActivityService $activityService
  ) {
  }

  /**
   * POST /checklists/{checklist}/items
   */
  public function store(Request $request, Checklist $checklist): JsonResponse
  {
    $board = $checklist->card->column->board;
    $this->authorize('editContent', $board);

    $validated = $request->validate([
      'title' => 'required|string|max:255',
    ]);

    // Get next position
    $maxPosition = $checklist->items()->max('position') ?? -1;

    $item = $checklist->items()->create([
      'title' => $validated['title'],
      'position' => $maxPosition + 1,
    ]);

    return response()->json(['data' => $item], 201);
  }

  /**
   * PATCH /checklist-items/{item}
   */
  public function update(Request $request, ChecklistItem $item): JsonResponse
  {
    $checklist = $item->checklist;
    $card = $checklist->card;
    $board = $card->column->board;
    $this->authorize('editContent', $board);

    $validated = $request->validate([
      'title' => 'sometimes|string|max:255',
      'is_completed' => 'sometimes|boolean',
      'position' => 'sometimes|integer|min:0',
    ]);

    $wasCompleted = $item->is_completed;

    // Handle completion status change
    if (isset($validated['is_completed'])) {
      if ($validated['is_completed'] && !$wasCompleted) {
        $validated['completed_at'] = now();
        // Log completion activity
        $this->activityService->logChecklistItemCompleted($card, $request->user(), $checklist, $item);
      } elseif (!$validated['is_completed'] && $wasCompleted) {
        $validated['completed_at'] = null;
        // Log uncomplete activity
        $this->activityService->logChecklistItemUncompleted($card, $request->user(), $checklist, $item);
      }
    }

    $item->update($validated);

    return response()->json(['data' => $item]);
  }

  /**
   * DELETE /checklist-items/{item}
   */
  public function destroy(ChecklistItem $item): JsonResponse
  {
    $board = $item->checklist->card->column->board;
    $this->authorize('editContent', $board);

    $item->delete();

    return response()->json(null, 204);
  }
}
