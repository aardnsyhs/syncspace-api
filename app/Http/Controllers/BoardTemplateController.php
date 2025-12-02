<?php

namespace App\Http\Controllers;

use App\Events\BoardCreated;
use App\Models\Board;
use App\Models\BoardTemplate;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BoardTemplateController extends Controller
{

  public function index(Request $request): JsonResponse
  {
    $user = $request->user();

    $teamIds = $user->teams()->pluck('teams.id');

    $templates = BoardTemplate::with('columns.cards')
      ->where(function ($query) use ($teamIds) {
        $query->where('visibility', 'global')
          ->orWhereIn('team_id', $teamIds);
      })
      ->orderBy('visibility')
      ->orderBy('name')
      ->get();

    return response()->json([
      'data' => $templates->map(fn($t) => $this->formatTemplate($t)),
    ]);
  }

  public function show(Request $request, BoardTemplate $template): JsonResponse
  {
    if (!$template->isAccessibleBy($request->user())) {
      abort(403, 'You do not have access to this template');
    }

    $template->load('columns.cards', 'creator:id,name');

    return response()->json([
      'data' => $this->formatTemplate($template, true),
    ]);
  }

  public function store(Request $request, Team $team): JsonResponse
  {
    $this->authorize('manageBoards', $team);

    $validated = $request->validate([
      'name' => 'required|string|max:100',
      'description' => 'nullable|string|max:500',
      'board_id' => 'nullable|exists:boards,id',
      'columns' => 'required_without:board_id|array',
      'columns.*.name' => 'required_with:columns|string|max:100',
      'columns.*.wip_limit' => 'nullable|integer|min:1',
    ]);

    $template = new BoardTemplate([
      'team_id' => $team->id,
      'name' => $validated['name'],
      'description' => $validated['description'] ?? null,
      'visibility' => 'team',
      'created_by' => $request->user()->id,
    ]);
    $template->save();

    if (!empty($validated['board_id'])) {

      $board = Board::with('columns.cards')->findOrFail($validated['board_id']);

      $this->authorize('view', $board);

      foreach ($board->columns as $column) {
        $templateColumn = $template->columns()->create([
          'name' => $column->name,
          'position' => $column->position,
          'wip_limit' => $column->wip_limit,
        ]);

        foreach ($column->cards->take(3) as $card) {
          $templateColumn->cards()->create([
            'title' => $card->title,
            'description' => $card->description,
            'position' => $card->position,
          ]);
        }
      }
    } else {

      foreach ($validated['columns'] as $index => $columnData) {
        $template->columns()->create([
          'name' => $columnData['name'],
          'position' => $index,
          'wip_limit' => $columnData['wip_limit'] ?? null,
        ]);
      }
    }

    $template->load('columns.cards');

    return response()->json([
      'data' => $this->formatTemplate($template),
    ], 201);
  }

  public function destroy(Request $request, BoardTemplate $template): JsonResponse
  {
    $user = $request->user();

    $canDelete = $template->created_by === $user->id;

    if (!$canDelete && $template->team_id) {
      $role = $template->team->getMemberRole($user);
      $canDelete = in_array($role, ['owner', 'admin']);
    }

    if (!$canDelete) {
      abort(403, 'You cannot delete this template');
    }

    $template->delete();

    return response()->json(null, 204);
  }

  public function createBoardFromTemplate(Request $request, Team $team): JsonResponse
  {
    $this->authorize('create', [Board::class, $team]);

    $validated = $request->validate([
      'template_id' => 'required|exists:board_templates,id',
      'name' => 'required|string|max:100',
      'description' => 'nullable|string|max:500',
      'color' => 'nullable|string|max:7',
    ]);

    $template = BoardTemplate::with('columns.cards')->findOrFail($validated['template_id']);

    if (!$template->isAccessibleBy($request->user())) {
      abort(403, 'You do not have access to this template');
    }

    $board = $team->boards()->create([
      'name' => $validated['name'],
      'description' => $validated['description'] ?? null,
      'color' => $validated['color'] ?? null,
    ]);

    foreach ($template->columns as $templateColumn) {
      $column = $board->columns()->create([
        'name' => $templateColumn->name,
        'position' => $templateColumn->position,
        'wip_limit' => $templateColumn->wip_limit,
      ]);

      foreach ($templateColumn->cards as $templateCard) {
        $column->cards()->create([
          'title' => $templateCard->title,
          'description' => $templateCard->description,
          'position' => $templateCard->position,
        ]);
      }
    }

    $board->load('columns.cards');

    broadcast(new BoardCreated($board))->toOthers();

    return response()->json([
      'data' => $board,
      'message' => "Board created from template '{$template->name}'",
    ], 201);
  }

  private function formatTemplate(BoardTemplate $template, bool $detailed = false): array
  {
    $data = [
      'id' => $template->id,
      'name' => $template->name,
      'description' => $template->description,
      'slug' => $template->slug,
      'visibility' => $template->visibility,
      'column_count' => $template->columns->count(),
      'created_at' => $template->created_at,
    ];

    if ($detailed) {
      $data['columns'] = $template->columns->map(fn($col) => [
        'id' => $col->id,
        'name' => $col->name,
        'position' => $col->position,
        'wip_limit' => $col->wip_limit,
        'sample_cards' => $col->cards->map(fn($card) => [
          'title' => $card->title,
          'description' => $card->description,
        ]),
      ]);
      $data['creator'] = $template->creator ? [
        'id' => $template->creator->id,
        'name' => $template->creator->name,
      ] : null;
    }

    return $data;
  }
}
