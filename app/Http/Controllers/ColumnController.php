<?php

namespace App\Http\Controllers;

use App\Events\ColumnCreated;
use App\Events\ColumnDeleted;
use App\Events\ColumnUpdated;
use App\Http\Requests\Column\MoveColumnRequest;
use App\Http\Requests\Column\StoreColumnRequest;
use App\Http\Requests\Column\UpdateColumnRequest;
use App\Http\Resources\ColumnResource;
use App\Models\Board;
use App\Models\Column;
use App\Services\ActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ColumnController extends Controller
{
  public function __construct(
    private ActivityService $activityService
  ) {
  }

  public function store(StoreColumnRequest $request, Board $board): JsonResponse
  {
    $this->authorize('create', [Column::class, $board]);

    $maxPosition = $board->columns()->max('position') ?? -1;

    $column = $board->columns()->create([
      'name' => $request->name,
      'position' => $maxPosition + 1,
    ]);

    $this->activityService->logColumnCreated($column, $request->user());
    broadcast(new ColumnCreated($column))->toOthers();

    return response()->json([
      'data' => new ColumnResource($column),
    ], 201);
  }

  public function update(UpdateColumnRequest $request, Column $column): JsonResponse
  {
    $this->authorize('update', $column);

    $column->update($request->validated());

    $this->activityService->logColumnUpdated($column, $request->user());
    broadcast(new ColumnUpdated($column))->toOthers();

    return response()->json([
      'data' => new ColumnResource($column),
    ]);
  }

  public function destroy(Request $request, Column $column): JsonResponse
  {
    $this->authorize('delete', $column);

    $board = $column->board;
    $boardId = $board->id;
    $columnId = $column->id;
    $columnName = $column->name;
    $position = $column->position;

    $column->delete();

    Column::where('board_id', $boardId)
      ->where('position', '>', $position)
      ->decrement('position');

    $this->activityService->logColumnDeleted($board, $request->user(), $columnName);
    broadcast(new ColumnDeleted($boardId, $columnId))->toOthers();

    return response()->json(null, 204);
  }

  public function move(MoveColumnRequest $request, Column $column): JsonResponse
  {
    $this->authorize('move', $column);

    $newPosition = $request->position;
    $oldPosition = $column->position;

    if ($newPosition === $oldPosition) {
      return response()->json(['data' => new ColumnResource($column)]);
    }

    DB::transaction(function () use ($column, $newPosition, $oldPosition) {
      if ($newPosition > $oldPosition) {
        $column->board->columns()
          ->whereBetween('position', [$oldPosition + 1, $newPosition])
          ->decrement('position');
      } else {
        $column->board->columns()
          ->whereBetween('position', [$newPosition, $oldPosition - 1])
          ->increment('position');
      }

      $column->update(['position' => $newPosition]);
    });

    broadcast(new ColumnUpdated($column->fresh()))->toOthers();

    return response()->json([
      'data' => new ColumnResource($column->fresh()),
    ]);
  }

  public function updateWipLimit(Request $request, Column $column): JsonResponse
  {
    $this->authorize('update', $column);

    $validated = $request->validate([
      'wip_limit' => 'nullable|integer|min:1|max:100',
    ]);

    $column->update(['wip_limit' => $validated['wip_limit']]);

    broadcast(new ColumnUpdated($column))->toOthers();

    return response()->json([
      'data' => new ColumnResource($column),
    ]);
  }
}
