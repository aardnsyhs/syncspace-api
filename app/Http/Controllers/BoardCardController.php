<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Card;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BoardCardController extends Controller
{
  /**
   * GET /boards/{board}/cards
   * Get filtered cards for a board
   * 
   * Query params:
   * - search: string (searches title and description)
   * - assignee_id: int (filter by assignee)
   * - labels[]: array of label IDs
   * - due: string (overdue|today|this_week|no_due)
   * - column_id: int (filter by specific column)
   * - my_cards: bool (only cards assigned to current user)
   */
  public function index(Request $request, Board $board): JsonResponse
  {
    $this->authorize('view', $board);

    $columnIds = $board->columns()->pluck('id');

    $query = Card::whereIn('column_id', $columnIds)
      ->with(['assignee:id,name,avatar_url', 'labels', 'column:id,name,position']);

    // Search filter (title + description)
    if ($search = $request->input('search')) {
      $query->where(function ($q) use ($search) {
        $q->where('title', 'like', "%{$search}%")
          ->orWhere('description', 'like', "%{$search}%");
      });
    }

    // Assignee filter
    if ($assigneeId = $request->input('assignee_id')) {
      $query->where('assignee_id', $assigneeId);
    }

    // My cards filter (current user's cards)
    if ($request->boolean('my_cards')) {
      $query->where('assignee_id', $request->user()->id);
    }

    // Labels filter (cards that have ANY of the specified labels)
    if ($labels = $request->input('labels')) {
      $labelIds = is_array($labels) ? $labels : explode(',', $labels);
      $query->whereHas('labels', function ($q) use ($labelIds) {
        $q->whereIn('labels.id', $labelIds);
      });
    }

    // Due date filter
    if ($due = $request->input('due')) {
      $today = now()->startOfDay();

      switch ($due) {
        case 'overdue':
          $query->whereNotNull('due_date')
            ->where('due_date', '<', $today);
          break;
        case 'today':
          $query->whereDate('due_date', $today);
          break;
        case 'this_week':
          $query->whereBetween('due_date', [
            $today,
            $today->copy()->endOfWeek()
          ]);
          break;
        case 'no_due':
          $query->whereNull('due_date');
          break;
      }
    }

    // Column filter
    if ($columnId = $request->input('column_id')) {
      $query->where('column_id', $columnId);
    }

    // Order by column position, then card position
    $query->orderBy('column_id')->orderBy('position');

    $cards = $query->get();

    // Group by column for easier frontend consumption
    $grouped = $cards->groupBy('column_id');

    // Get column info with WIP status
    $columns = $board->columns()
      ->withCount('cards')
      ->orderBy('position')
      ->get()
      ->map(function ($column) {
        return [
          'id' => $column->id,
          'name' => $column->name,
          'position' => $column->position,
          'wip_limit' => $column->wip_limit,
          'card_count' => $column->cards_count,
          'wip_exceeded' => $column->wip_limit !== null && $column->cards_count > $column->wip_limit,
        ];
      });

    return response()->json([
      'data' => [
        'cards' => $cards,
        'columns' => $columns,
        'total_count' => $cards->count(),
        'filters_applied' => $this->getAppliedFilters($request),
      ],
    ]);
  }

  private function getAppliedFilters(Request $request): array
  {
    $filters = [];

    if ($request->filled('search'))
      $filters[] = 'search';
    if ($request->filled('assignee_id'))
      $filters[] = 'assignee';
    if ($request->filled('labels'))
      $filters[] = 'labels';
    if ($request->filled('due'))
      $filters[] = 'due_date';
    if ($request->filled('column_id'))
      $filters[] = 'column';
    if ($request->boolean('my_cards'))
      $filters[] = 'my_cards';

    return $filters;
  }
}
