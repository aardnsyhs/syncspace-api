<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
  public function __invoke(RegisterRequest $request): JsonResponse
  {
    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => $request->password, // auto-hashed via cast
    ]);

    // Create a personal team for the new user
    $team = Team::create([
      'name' => $user->name . "'s Team",
      'slug' => Str::slug($user->name) . '-' . Str::random(6),
      'owner_id' => $user->id,
    ]);

    // Add user as owner of the team
    $team->members()->attach($user->id, ['role' => 'owner']);

    $token = $user->createToken('auth-token')->plainTextToken;

    return response()->json([
      'data' => new UserResource($user),
      'token' => $token,
    ], 201);
  }
}
