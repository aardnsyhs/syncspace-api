<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Services\BoardAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BoardAnalyticsController extends Controller
{
  public function __construct(
    private BoardAnalyticsService $analyticsService
  ) {
  }

  /**
   * GET /boards/{board}/analytics/summary
   * Returns summary metrics for the board
   */
  public function summary(Board $board): JsonResponse
  {
    $this->authorize('view', $board);

    $data = $this->analyticsService->getSummary($board);

    return response()->json(['data' => $data]);
  }

  /**
   * GET /boards/{board}/analytics/throughput?weeks=6
   * Returns throughput data for chart
   */
  public function throughput(Request $request, Board $board): JsonResponse
  {
    $this->authorize('view', $board);

    $weeks = $request->integer('weeks', 6);
    $weeks = min(max($weeks, 1), 52); // Clamp between 1-52 weeks

    $data = $this->analyticsService->getThroughput($board, $weeks);

    return response()->json($data);
  }

  /**
   * GET /boards/{board}/analytics/cumulative-flow?days=30
   * Returns data for Cumulative Flow Diagram
   */
  public function cumulativeFlow(Request $request, Board $board): JsonResponse
  {
    $this->authorize('view', $board);

    $days = $request->integer('days', 30);
    $days = min(max($days, 7), 90); // Clamp between 7-90 days

    $data = $this->analyticsService->getCumulativeFlow($board, $days);

    return response()->json($data);
  }

  /**
   * GET /boards/{board}/analytics/assignees
   * Returns card distribution by assignee
   */
  public function assignees(Board $board): JsonResponse
  {
    $this->authorize('view', $board);

    $data = $this->analyticsService->getAssigneeDistribution($board);

    return response()->json($data);
  }
}
