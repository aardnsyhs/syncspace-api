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

  public function summary(Board $board): JsonResponse
  {
    $this->authorize('view', $board);

    $data = $this->analyticsService->getSummary($board);

    return response()->json(['data' => $data]);
  }

  public function throughput(Request $request, Board $board): JsonResponse
  {
    $this->authorize('view', $board);

    $weeks = $request->integer('weeks', 6);
    $weeks = min(max($weeks, 1), 52); 

    $data = $this->analyticsService->getThroughput($board, $weeks);

    return response()->json($data);
  }

  public function cumulativeFlow(Request $request, Board $board): JsonResponse
  {
    $this->authorize('view', $board);

    $days = $request->integer('days', 30);
    $days = min(max($days, 7), 90); 

    $data = $this->analyticsService->getCumulativeFlow($board, $days);

    return response()->json($data);
  }

  public function assignees(Board $board): JsonResponse
  {
    $this->authorize('view', $board);

    $data = $this->analyticsService->getAssigneeDistribution($board);

    return response()->json($data);
  }
}
