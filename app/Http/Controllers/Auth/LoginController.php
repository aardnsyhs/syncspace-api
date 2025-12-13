<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
  public function __construct(
    private UserService $userService
  ) {
  }

  public function __invoke(LoginRequest $request): JsonResponse
  {
    $user = User::where('email', $request->email)->first();

    if ($user && $user->google_id) {
      throw ValidationException::withMessages([
        'email' => ['This account was created with Google. Please use Google to sign in.'],
      ]);
    }

    if (!Auth::attempt($request->only('email', 'password'))) {
      throw ValidationException::withMessages([
        'email' => ['The provided credentials are incorrect.'],
      ]);
    }

    $user = Auth::user();

    $this->userService->ensureHasWorkspace($user);

    $token = $user->createToken('auth-token')->plainTextToken;

    return response()->json([
      'data' => new UserResource($user),
      'token' => $token,
    ]);
  }
}
