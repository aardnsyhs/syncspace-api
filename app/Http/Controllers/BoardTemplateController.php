<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\BoardTemplate;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BoardTemplateController extends Controller
{
  /**
   * GET /board-templates
   * List templates accessible to user (global + team templates)
   */
  public function index(Request $request): JsonResponse
  {
    $user = $request->user();

    // Get user's team IDs
    $teamIds = $user->teams()->pluck('teams.id');

    // Get global templates + team templates
    $templates = BoardTemplate::with('columns.cards')
      ->where(function ($query) use ($teamIds) {
        $query->where('visibility', 'global')
          ->orWhereIn('team_id', $teamIds);
      })
      ->orderBy('visibility') // global first
      ->orderBy('name')
      ->get();

    return response()->json([
      'data' => $templates->map(fn($t) => $this->formatTemplate($t)),
    ]);
  }

  /**
   * GET /board-templates/{template}
   * Get template detail
   */
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

  /**
   * POST /teams/{team}/board-templates
   * Create template from existing board or new definition
   */
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
      // Copy from existing board
      $board = Board::with('columns.cards')->findOrFail($validated['board_id']);

      // Verify user has access to this board
      $this->authorize('view', $board);

      foreach ($board->columns as $column) {
        $templateColumn = $template->columns()->create([
          'name' => $column->name,
          'position' => $column->position,
          'wip_limit' => $column->wip_limit,
        ]);

        // Copy sample cards (first 3 per column)
        foreach ($column->cards->take(3) as $card) {
          $templateColumn->cards()->create([
            'title' => $card->title,
            'description' => $card->description,
            'position' => $card->position,
          ]);
        }
      }
    } else {
      // Create from definition
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

  /**
   * DELETE /board-templates/{template}
   */
  public function destroy(Request $request, BoardTemplate $template): JsonResponse
  {
    $user = $request->user();

    // Only creator or team owner/admin can delete
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

  /**
   * POST /teams/{team}/boards/from-template
   * Create board from template
   */
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

    // Create board
    $board = $team->boards()->create([
      'name' => $validated['name'],
      'description' => $validated['description'] ?? null,
      'color' => $validated['color'] ?? null,
    ]);

    // Copy columns and cards from template
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
