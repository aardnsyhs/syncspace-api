<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Label;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BoardLabelController extends Controller
{

  public function index(Board $board): JsonResponse
  {
    $this->authorize('view', $board);

    $labels = $board->labels()->orderBy('name')->get();

    return response()->json(['data' => $labels]);
  }

  public function store(Request $request, Board $board): JsonResponse
  {
    $this->authorize('editContent', $board);

    $validated = $request->validate([
      'name' => 'required|string|max:50',
      'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);

    $label = $board->labels()->create($validated);

    return response()->json(['data' => $label], 201);
  }

  public function update(Request $request, Board $board, Label $label): JsonResponse
  {
    $this->authorize('editContent', $board);

    if ($label->board_id !== $board->id) {
      abort(404);
    }

    $validated = $request->validate([
      'name' => 'sometimes|string|max:50',
      'color' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);

    $label->update($validated);

    return response()->json(['data' => $label]);
  }

  public function destroy(Board $board, Label $label): JsonResponse
  {
    $this->authorize('editContent', $board);

    if ($label->board_id !== $board->id) {
      abort(404);
    }

    $label->delete();

    return response()->json(null, 204);
  }
}
