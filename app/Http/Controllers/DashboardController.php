<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Card;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{

  public function stats(Request $request): JsonResponse
  {
    $user = $request->user();
    $teamIds = $user->teams()->pluck('teams.id');

    $boardIds = Board::whereIn('team_id', $teamIds)->pluck('id');

    $totalBoards = $boardIds->count();

    $totalCards = Card::whereHas('column', function ($q) use ($boardIds) {
      $q->whereIn('board_id', $boardIds);
    })->count();

    $cardsDueSoon = Card::whereHas('column', function ($q) use ($boardIds) {
      $q->whereIn('board_id', $boardIds);
    })
      ->whereNotNull('due_date')
      ->where('due_date', '>=', Carbon::now())
      ->where('due_date', '<=', Carbon::now()->addDays(7))
      ->count();

    // Use the is_completed flag for accurate completion tracking.
    // This is more reliable than matching column names, which can vary per board.
    $completedCards = Card::where('is_completed', true)
      ->whereHas('column', function ($q) use ($boardIds) {
        $q->whereIn('board_id', $boardIds);
      })->count();

    return response()->json([
      'data' => [
        'total_boards' => $totalBoards,
        'total_cards' => $totalCards,
        'cards_due_soon' => $cardsDueSoon,
        'completed_cards' => $completedCards,
      ],
    ]);
  }

  public function activities(Request $request): JsonResponse
  {
    $user = $request->user();
    $limit = $request->input('limit', 10);
    $teamIds = $user->teams()->pluck('teams.id');
    $boardIds = Board::whereIn('team_id', $teamIds)->pluck('id');

    $activities = Activity::whereIn('board_id', $boardIds)
      ->with(['user:id,name,email,avatar_url', 'board:id,name'])
      ->orderBy('created_at', 'desc')
      ->limit($limit)
      ->get()
      ->map(function ($activity) {
        return [
          'id' => $activity->id,
          'description' => $activity->description,
          'created_at' => $activity->created_at,
          'user' => [
            'name' => $activity->user->name ?? 'Unknown',
            'avatar_url' => $activity->user->avatar_url ?? null,
          ],
          'board' => [
            'id' => $activity->board->id ?? null,
            'name' => $activity->board->name ?? 'Unknown',
          ],
        ];
      });

    return response()->json([
      'data' => $activities,
    ]);
  }

  public function myCards(Request $request): JsonResponse
  {
    $user = $request->user();
    $limit = $request->input('limit', 10);
    $teamIds = $user->teams()->pluck('teams.id');
    $boardIds = Board::whereIn('team_id', $teamIds)->pluck('id');

    $cards = Card::where('assignee_id', $user->id)
      ->whereHas('column', function ($q) use ($boardIds) {
        $q->whereIn('board_id', $boardIds);
      })
      ->with([
        'column:id,name,board_id',
        'column.board:id,name',
      ])
      ->orderByRaw('due_date IS NULL, due_date ASC')
      ->limit($limit)
      ->get()
      ->map(function ($card) {
        return [
          'id' => $card->id,
          'title' => $card->title,
          'due_date' => $card->due_date,
          'board' => [
            'id' => $card->column->board->id ?? null,
            'name' => $card->column->board->name ?? 'Unknown',
          ],
          'column' => [
            'name' => $card->column->name ?? 'Unknown',
          ],
        ];
      });

    return response()->json([
      'data' => $cards,
    ]);
  }
}
