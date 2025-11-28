<?php

namespace App\Http\Controllers;

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
    $this->authorizeTeamAccess($request, $team);

    $boards = $team->boards()->latest()->get();

    return BoardResource::collection($boards);
  }

  public function store(StoreBoardRequest $request, Team $team): JsonResponse
  {
    $this->authorizeTeamAccess($request, $team);

    $board = $team->boards()->create($request->validated());

    // Create default columns
    $defaultColumns = ['To Do', 'In Progress', 'Review', 'Done'];
    foreach ($defaultColumns as $index => $name) {
      $board->columns()->create([
        'name' => $name,
        'position' => $index,
      ]);
    }

    return response()->json([
      'data' => new BoardResource($board->load('columns')),
    ], 201);
  }

  public function show(Request $request, Board $board): JsonResponse
  {
    $this->authorizeTeamAccess($request, $board->team);

    // Load full board state with columns, cards, and related data
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
    $this->authorizeTeamAccess($request, $board->team);

    $board->update($request->validated());

    return response()->json([
      'data' => new BoardResource($board),
    ]);
  }

  public function destroy(Request $request, Board $board): JsonResponse
  {
    $this->authorizeTeamAdmin($request, $board->team);

    $board->delete();

    return response()->json(null, 204);
  }

  private function authorizeTeamAccess(Request $request, Team $team): void
  {
    if (!$team->hasMember($request->user())) {
      abort(403, 'You are not a member of this team.');
    }
  }

  private function authorizeTeamAdmin(Request $request, Team $team): void
  {
    $role = $team->getMemberRole($request->user());
    if (!in_array($role, ['owner', 'admin'])) {
      abort(403, 'You do not have permission to delete this board.');
    }
  }
}
