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

  public function index(Card $card): JsonResponse
  {
    $board = $card->column->board;
    $this->authorize('view', $board);

    $checklists = $card->checklists()->with('items')->get();

    $checklistsWithProgress = $checklists->map(function ($checklist) {
      $checklist->setAttribute('progress', $checklist->progress);
      return $checklist;
    });

    return response()->json(['data' => $checklistsWithProgress]);
  }

  public function store(Request $request, Card $card): JsonResponse
  {
    $board = $card->column->board;
    $this->authorize('editContent', $board);

    $validated = $request->validate([
      'title' => 'required|string|max:255',
    ]);

    $maxPosition = $card->checklists()->max('position') ?? -1;

    $checklist = $card->checklists()->create([
      'title' => $validated['title'],
      'position' => $maxPosition + 1,
    ]);

    $this->activityService->logChecklistAdded($card, $request->user(), $checklist);

    return response()->json([
      'data' => $checklist->load('items'),
    ], 201);
  }

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

  public function destroy(Request $request, Checklist $checklist): JsonResponse
  {
    $card = $checklist->card;
    $board = $card->column->board;
    $this->authorize('editContent', $board);

    $checklistTitle = $checklist->title;
    $checklist->delete();

    $this->activityService->logChecklistRemoved($card, $request->user(), $checklistTitle);

    return response()->json(null, 204);
  }
}
