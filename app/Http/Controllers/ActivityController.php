<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ActivityController extends Controller
{
  public function index(Request $request, Board $board): AnonymousResourceCollection
  {
    $this->authorize('view', $board);

    $activities = $board->activities()
      ->with('user')
      ->orderByDesc('created_at')
      ->paginate(20);

    return ActivityResource::collection($activities);
  }
}
