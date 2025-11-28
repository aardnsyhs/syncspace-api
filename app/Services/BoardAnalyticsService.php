<?php

namespace App\Services;

use App\Models\Board;
use App\Models\Card;
use App\Models\CardTransition;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BoardAnalyticsService
{
  /**
   * Get summary analytics for a board
   */
  public function getSummary(Board $board): array
  {
    $columns = $board->columns()->orderBy('position')->get();
    $columnIds = $columns->pluck('id')->toArray();

    if (empty($columnIds)) {
      return $this->emptyResponse();
    }

    // Cards per column
    $cardsPerColumn = Card::whereIn('column_id', $columnIds)
      ->select('column_id', DB::raw('COUNT(*) as count'))
      ->groupBy('column_id')
      ->pluck('count', 'column_id')
      ->toArray();

    $columnStats = $columns->map(fn($col) => [
      'id' => $col->id,
      'name' => $col->name,
      'position' => $col->position,
      'card_count' => $cardsPerColumn[$col->id] ?? 0,
    ]);

    // Done column (last column)
    $doneColumnId = $columns->last()?->id;

    // Completed cards in last 7 and 30 days
    $completed7Days = $this->getCompletedCount($doneColumnId, 7);
    $completed30Days = $this->getCompletedCount($doneColumnId, 30);

    // Average cycle time for cards completed in last 30 days
    $avgCycleTime = $this->getAverageCycleTime($board, $doneColumnId, 30);
    $avgLeadTime = $this->getAverageLeadTime($board, $doneColumnId, 30);

    // Total cards
    $totalCards = array_sum($cardsPerColumn);

    // WIP (cards not in first or last column)
    $wipCount = $columns->slice(1, -1)->sum(fn($col) => $cardsPerColumn[$col->id] ?? 0);

    return [
      'total_cards' => $totalCards,
      'columns' => $columnStats,
      'completed_last_7_days' => $completed7Days,
      'completed_last_30_days' => $completed30Days,
      'avg_cycle_time_days' => $avgCycleTime,
      'avg_lead_time_days' => $avgLeadTime,
      'wip_count' => $wipCount,
    ];
  }

  /**
   * Get throughput data (completed cards per period)
   */
  public function getThroughput(Board $board, int $weeks = 6): array
  {
    $columns = $board->columns()->orderBy('position')->get();
    $doneColumnId = $columns->last()?->id;

    if (!$doneColumnId) {
      return ['data' => []];
    }

    $startDate = now()->subWeeks($weeks)->startOfWeek();

    $throughput = CardTransition::where('to_column_id', $doneColumnId)
      ->where('transitioned_at', '>=', $startDate)
      ->select(
        DB::raw('YEARWEEK(transitioned_at, 1) as year_week'),
        DB::raw('MIN(DATE(transitioned_at)) as week_start'),
        DB::raw('COUNT(DISTINCT card_id) as completed_count')
      )
      ->groupBy('year_week')
      ->orderBy('year_week')
      ->get();

    // Fill in missing weeks with zero
    $result = [];
    $current = $startDate->copy();
    $end = now()->endOfWeek();

    while ($current <= $end) {
      $yearWeek = $current->format('oW');
      $weekData = $throughput->firstWhere('year_week', $yearWeek);

      $result[] = [
        'week_start' => $current->format('Y-m-d'),
        'week_end' => $current->copy()->endOfWeek()->format('Y-m-d'),
        'completed_count' => $weekData?->completed_count ?? 0,
      ];

      $current->addWeek();
    }

    return ['data' => $result];
  }

  /**
   * Get cumulative flow data for CFD chart
   */
  public function getCumulativeFlow(Board $board, int $days = 30): array
  {
    $columns = $board->columns()->orderBy('position')->get();
    $columnIds = $columns->pluck('id')->toArray();

    if (empty($columnIds)) {
      return ['data' => [], 'columns' => []];
    }

    $startDate = now()->subDays($days)->startOfDay();
    $result = [];

    // For each day, calculate how many cards were in each column at end of day
    for ($i = 0; $i <= $days; $i++) {
      $date = $startDate->copy()->addDays($i);
      $endOfDay = $date->copy()->endOfDay();

      $dayData = [
        'date' => $date->format('Y-m-d'),
        'columns' => [],
      ];

      foreach ($columns as $column) {
        // Count cards that were in this column at end of day
        // A card is in column X at time T if:
        // 1. Its last transition before T was to column X, OR
        // 2. It was created in column X and never moved before T

        $count = $this->getCardCountInColumnAtTime($column->id, $endOfDay, $columnIds);
        $dayData['columns'][$column->id] = $count;
      }

      $result[] = $dayData;
    }

    return [
      'data' => $result,
      'columns' => $columns->map(fn($c) => [
        'id' => $c->id,
        'name' => $c->name,
        'position' => $c->position,
      ]),
    ];
  }

  /**
   * Get cards distribution by assignee
   */
  public function getAssigneeDistribution(Board $board): array
  {
    $columnIds = $board->columns()->pluck('id')->toArray();

    if (empty($columnIds)) {
      return ['data' => []];
    }

    $distribution = Card::whereIn('column_id', $columnIds)
      ->leftJoin('users', 'cards.assignee_id', '=', 'users.id')
      ->select(
        'assignee_id',
        DB::raw('users.name as assignee_name'),
        DB::raw('users.avatar_url as assignee_avatar'),
        DB::raw('COUNT(*) as card_count')
      )
      ->groupBy('assignee_id', 'users.name', 'users.avatar_url')
      ->get()
      ->map(fn($row) => [
        'assignee_id' => $row->assignee_id,
        'assignee_name' => $row->assignee_name ?? 'Unassigned',
        'assignee_avatar' => $row->assignee_avatar,
        'card_count' => $row->card_count,
      ]);

    return ['data' => $distribution];
  }

  private function getCompletedCount(?int $doneColumnId, int $days): int
  {
    if (!$doneColumnId)
      return 0;

    return CardTransition::where('to_column_id', $doneColumnId)
      ->where('transitioned_at', '>=', now()->subDays($days))
      ->distinct('card_id')
      ->count('card_id');
  }

  private function getAverageCycleTime(Board $board, ?int $doneColumnId, int $days): ?float
  {
    if (!$doneColumnId)
      return null;

    $columns = $board->columns()->orderBy('position')->get();
    $firstColumnId = $columns->first()?->id;

    if (!$firstColumnId || $columns->count() < 2)
      return null;

    // Get cards completed in last N days
    $completedCardIds = CardTransition::where('to_column_id', $doneColumnId)
      ->where('transitioned_at', '>=', now()->subDays($days))
      ->pluck('card_id')
      ->unique();

    if ($completedCardIds->isEmpty())
      return null;

    $cycleTimes = [];

    foreach ($completedCardIds as $cardId) {
      // First time card left the first column (started)
      $startedAt = CardTransition::where('card_id', $cardId)
        ->where('from_column_id', $firstColumnId)
        ->orderBy('transitioned_at')
        ->value('transitioned_at');

      // Last time card entered done column
      $completedAt = CardTransition::where('card_id', $cardId)
        ->where('to_column_id', $doneColumnId)
        ->orderByDesc('transitioned_at')
        ->value('transitioned_at');

      if ($startedAt && $completedAt) {
        $cycleTimes[] = Carbon::parse($startedAt)->diffInHours(Carbon::parse($completedAt)) / 24;
      }
    }

    return count($cycleTimes) > 0 ? round(array_sum($cycleTimes) / count($cycleTimes), 1) : null;
  }

  private function getAverageLeadTime(Board $board, ?int $doneColumnId, int $days): ?float
  {
    if (!$doneColumnId)
      return null;

    // Get cards completed in last N days with their creation time
    $completedCards = CardTransition::where('to_column_id', $doneColumnId)
      ->where('transitioned_at', '>=', now()->subDays($days))
      ->join('cards', 'card_transitions.card_id', '=', 'cards.id')
      ->select('cards.id', 'cards.created_at', DB::raw('MAX(card_transitions.transitioned_at) as completed_at'))
      ->groupBy('cards.id', 'cards.created_at')
      ->get();

    if ($completedCards->isEmpty())
      return null;

    $leadTimes = $completedCards->map(function ($card) {
      return Carbon::parse($card->created_at)->diffInHours(Carbon::parse($card->completed_at)) / 24;
    });

    return round($leadTimes->average(), 1);
  }

  private function getCardCountInColumnAtTime(int $columnId, Carbon $time, array $boardColumnIds): int
  {
    // Cards currently in this column that existed at that time
    return DB::table('cards')
      ->whereIn('column_id', $boardColumnIds)
      ->where('created_at', '<=', $time)
      ->where(function ($query) use ($columnId, $time) {
        $query->where(function ($q) use ($columnId, $time) {
          // Card's last transition before $time was to this column
          $q->whereExists(function ($sub) use ($columnId, $time) {
            $sub->select(DB::raw(1))
              ->from('card_transitions as ct')
              ->whereColumn('ct.card_id', 'cards.id')
              ->where('ct.transitioned_at', '<=', $time)
              ->where('ct.to_column_id', $columnId)
              ->whereNotExists(function ($inner) use ($time) {
                $inner->select(DB::raw(1))
                  ->from('card_transitions as ct2')
                  ->whereColumn('ct2.card_id', 'ct.card_id')
                  ->where('ct2.transitioned_at', '<=', $time)
                  ->whereColumn('ct2.transitioned_at', '>', 'ct.transitioned_at');
              });
          });
        })->orWhere(function ($q) use ($columnId, $time) {
          // Card was created in this column and never moved before $time
          $q->where('column_id', $columnId)
            ->whereNotExists(function ($sub) use ($time) {
            $sub->select(DB::raw(1))
              ->from('card_transitions as ct')
              ->whereColumn('ct.card_id', 'cards.id')
              ->where('ct.transitioned_at', '<=', $time);
          });
        });
      })
      ->count();
  }

  private function emptyResponse(): array
  {
    return [
      'total_cards' => 0,
      'columns' => [],
      'completed_last_7_days' => 0,
      'completed_last_30_days' => 0,
      'avg_cycle_time_days' => null,
      'avg_lead_time_days' => null,
      'wip_count' => 0,
    ];
  }
}
