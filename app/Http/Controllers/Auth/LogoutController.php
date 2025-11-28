<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
  public function __invoke(Request $request): JsonResponse
  {
    // Revoke current token
    $request->user()->currentAccessToken()->delete();

    return response()->json([
      'message' => 'Logged out successfully.',
    ]);
  }
}
