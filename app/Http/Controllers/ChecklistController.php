<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Checklist;
use App\Services\ActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
  public function __construct(
    private ActivityService $activityService
  ) {
  }

  /**
   * GET /cards/{card}/checklists
   */
  public function index(Card $card): JsonResponse
  {
    $board = $card->column->board;
    $this->authorize('view', $board);

    $checklists = $card->checklists()->with('items')->get();

    // Add progress to each checklist
    $checklistsWithProgress = $checklists->map(function ($checklist) {
      $checklist->setAttribute('progress', $checklist->progress);
      return $checklist;
    });

    return response()->json(['data' => $checklistsWithProgress]);
  }

  /**
   * POST /cards/{card}/checklists
   */
  public function store(Request $request, Card $card): JsonResponse
  {
    $board = $card->column->board;
    $this->authorize('editContent', $board);

    $validated = $request->validate([
      'title' => 'required|string|max:255',
    ]);

    // Get next position
    $maxPosition = $card->checklists()->max('position') ?? -1;

    $checklist = $card->checklists()->create([
      'title' => $validated['title'],
      'position' => $maxPosition + 1,
    ]);

    // Log activity
    $this->activityService->logChecklistAdded($card, $request->user(), $checklist);

    return response()->json([
      'data' => $checklist->load('items'),
    ], 201);
  }

  /**
   * PATCH /checklists/{checklist}
   */
  public function update(Request $request, Checklist $checklist): JsonResponse
  {
    $board = $checklist->card->column->board;
    $this->authorize('editContent', $board);

    $validated = $request->validate([
      'title' => 'sometimes|string|max:255',
      'position' => 'sometimes|integer|min:0',
    ]);

    $checklist->update($validated);

    return response()->json([
      'data' => $checklist->load('items'),
    ]);
  }

  /**
   * DELETE /checklists/{checklist}
   */
  public function destroy(Request $request, Checklist $checklist): JsonResponse
  {
    $card = $checklist->card;
    $board = $card->column->board;
    $this->authorize('editContent', $board);

    $checklistTitle = $checklist->title;
    $checklist->delete();

    // Log activity
    $this->activityService->logChecklistRemoved($card, $request->user(), $checklistTitle);

    return response()->json(null, 204);
  }
}
