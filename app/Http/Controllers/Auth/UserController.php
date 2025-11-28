<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function __invoke(Request $request): JsonResponse
  {
    return response()->json([
      'data' => new UserResource($request->user()),
    ]);
  }
}
