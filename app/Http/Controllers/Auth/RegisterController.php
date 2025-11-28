<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
  public function __invoke(RegisterRequest $request): JsonResponse
  {
    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => $request->password, // auto-hashed via cast
    ]);

    $token = $user->createToken('auth-token')->plainTextToken;

    return response()->json([
      'data' => new UserResource($user),
      'token' => $token,
    ], 201);
  }
}
