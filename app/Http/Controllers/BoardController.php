<?php

namespace App\Http\Controllers;

use App\Events\BoardCreated;
use App\Events\BoardDeleted;
use App\Events\BoardUpdated;
use App\Http\Requests\Board\StoreBoardRequest;
use App\Http\Requests\Board\UpdateBoardRequest;
use App\Http\Resources\BoardResource;
use App\Models\Board;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BoardController extends Controller
{
  public function index(Request $request, Team $team): AnonymousResourceCollection
  {
    $this->authorize('viewAny', [Board::class, $team]);

    $boards = $team->boards()->latest()->get();

    return BoardResource::collection($boards);
  }

  public function store(StoreBoardRequest $request, Team $team): JsonResponse
  {
    $this->authorize('create', [Board::class, $team]);

    $board = $team->boards()->create($request->validated());

    $defaultColumns = ['To Do', 'In Progress', 'Review', 'Done'];
    foreach ($defaultColumns as $index => $name) {
      $board->columns()->create([
        'name' => $name,
        'position' => $index,
      ]);
    }

    broadcast(new BoardCreated($board))->toOthers();

    return response()->json([
      'data' => new BoardResource($board->load('columns')),
    ], 201);
  }

  public function show(Request $request, Board $board): JsonResponse
  {
    $this->authorize('view', $board);

    $board->load([
      'columns.cards' => fn($q) => $q->orderBy('position'),
      'columns.cards.assignee',
      'columns.cards.labels',
    ]);

    return response()->json([
      'data' => new BoardResource($board),
    ]);
  }

  public function update(UpdateBoardRequest $request, Board $board): JsonResponse
  {
    $this->authorize('update', $board);

    $board->update($request->validated());

    broadcast(new BoardUpdated($board))->toOthers();

    return response()->json([
      'data' => new BoardResource($board),
    ]);
  }

  public function destroy(Request $request, Board $board): JsonResponse
  {
    $this->authorize('delete', $board);

    $teamId = $board->team_id;
    $boardId = $board->id;

    $board->delete();

    broadcast(new BoardDeleted($teamId, $boardId))->toOthers();

    return response()->json(null, 204);
  }
}
