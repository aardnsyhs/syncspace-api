<?php

namespace App\Http\Controllers;

use App\Http\Requests\Column\MoveColumnRequest;
use App\Http\Requests\Column\StoreColumnRequest;
use App\Http\Requests\Column\UpdateColumnRequest;
use App\Http\Resources\ColumnResource;
use App\Models\Board;
use App\Models\Column;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ColumnController extends Controller
{
  public function store(StoreColumnRequest $request, Board $board): JsonResponse
  {
    $this->authorizeTeamAccess($request, $board);

    // Get max position and add new column at the end
    $maxPosition = $board->columns()->max('position') ?? -1;

    $column = $board->columns()->create([
      'name' => $request->name,
      'position' => $maxPosition + 1,
    ]);

    return response()->json([
      'data' => new ColumnResource($column),
    ], 201);
  }

  public function update(UpdateColumnRequest $request, Column $column): JsonResponse
  {
    $this->authorizeTeamAccess($request, $column->board);

    $column->update($request->validated());

    return response()->json([
      'data' => new ColumnResource($column),
    ]);
  }

  public function destroy(Request $request, Column $column): JsonResponse
  {
    $this->authorizeTeamAccess($request, $column->board);

    $column->delete();

    // Reorder remaining columns
    $column->board->columns()
      ->where('position', '>', $column->position)
      ->decrement('position');

    return response()->json(null, 204);
  }

  public function move(MoveColumnRequest $request, Column $column): JsonResponse
  {
    $this->authorizeTeamAccess($request, $column->board);

    $newPosition = $request->position;
    $oldPosition = $column->position;

    if ($newPosition === $oldPosition) {
      return response()->json(['data' => new ColumnResource($column)]);
    }

    DB::transaction(function () use ($column, $newPosition, $oldPosition) {
      if ($newPosition > $oldPosition) {
        // Moving down: shift columns up
        $column->board->columns()
          ->whereBetween('position', [$oldPosition + 1, $newPosition])
          ->decrement('position');
      } else {
        // Moving up: shift columns down
        $column->board->columns()
          ->whereBetween('position', [$newPosition, $oldPosition - 1])
          ->increment('position');
      }

      $column->update(['position' => $newPosition]);
    });

    return response()->json([
      'data' => new ColumnResource($column->fresh()),
    ]);
  }

  private function authorizeTeamAccess(Request $request, Board $board): void
  {
    if (!$board->team->hasMember($request->user())) {
      abort(403, 'You are not a member of this team.');
    }
  }
}
