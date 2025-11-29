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
  public function update(Request $request, ChecklistItem $checklistItem): JsonResponse
  {
    // Eager load relationships to avoid null issues
    $checklistItem->load('checklist.card.column.board');

    $checklist = $checklistItem->checklist;
    if (!$checklist || !$checklist->card) {
      return response()->json(['message' => 'Checklist or card not found'], 404);
    }

    $card = $checklist->card;
    $board = $card->column->board;
    $this->authorize('editContent', $board);

    $validated = $request->validate([
      'title' => 'sometimes|string|max:255',
      'is_completed' => 'sometimes|boolean',
      'position' => 'sometimes|integer|min:0',
    ]);

    $wasCompleted = $checklistItem->is_completed;

    // Handle completion status change
    if (isset($validated['is_completed'])) {
      if ($validated['is_completed'] && !$wasCompleted) {
        $validated['completed_at'] = now();
        // Log completion activity
        $this->activityService->logChecklistItemCompleted($card, $request->user(), $checklist, $checklistItem);
      } elseif (!$validated['is_completed'] && $wasCompleted) {
        $validated['completed_at'] = null;
        // Log uncomplete activity
        $this->activityService->logChecklistItemUncompleted($card, $request->user(), $checklist, $checklistItem);
      }
    }

    $checklistItem->update($validated);

    return response()->json(['data' => $checklistItem]);
  }

  /**
   * DELETE /checklist-items/{item}
   */
  public function destroy(ChecklistItem $checklistItem): JsonResponse
  {
    $checklistItem->load('checklist.card.column.board');

    if (!$checklistItem->checklist || !$checklistItem->checklist->card) {
      return response()->json(['message' => 'Checklist or card not found'], 404);
    }

    $board = $checklistItem->checklist->card->column->board;
    $this->authorize('editContent', $board);

    $checklistItem->delete();

    return response()->json(null, 204);
  }
}
