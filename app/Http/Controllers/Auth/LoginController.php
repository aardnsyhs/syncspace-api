<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
  public function __invoke(LoginRequest $request): JsonResponse
  {
    if (!Auth::attempt($request->only('email', 'password'))) {
      throw ValidationException::withMessages([
        'email' => ['The provided credentials are incorrect.'],
      ]);
    }

    $user = Auth::user();
    $token = $user->createToken('auth-token')->plainTextToken;

    return response()->json([
      'data' => new UserResource($user),
      'token' => $token,
    ]);
  }
}
