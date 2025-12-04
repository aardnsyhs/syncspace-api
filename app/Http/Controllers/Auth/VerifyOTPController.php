<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\EmailVerification;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VerifyOTPController extends Controller
{
  public function __construct(
    private UserService $userService
  ) {
  }

  public function __invoke(Request $request): JsonResponse
  {
    $request->validate([
      'email' => ['required', 'email', 'exists:users,email'],
      'otp' => ['required', 'string', 'size:6'],
    ]);

    $verification = EmailVerification::where('email', $request->email)
      ->where('otp', $request->otp)
      ->whereNull('verified_at')
      ->first();

    if (!$verification) {
      throw ValidationException::withMessages([
        'otp' => ['The verification code is invalid.'],
      ]);
    }

    if ($verification->isExpired()) {
      throw ValidationException::withMessages([
        'otp' => ['The verification code has expired. Please request a new one.'],
      ]);
    }

    $verification->update(['verified_at' => now()]);

    $user = User::where('email', $request->email)->first();
    $user->email_verified_at = now();
    $user->save();

    $this->userService->ensureHasWorkspace($user);

    $token = $user->createToken('auth-token')->plainTextToken;

    return response()->json([
      'message' => 'Email verified successfully!',
      'data' => new UserResource($user),
      'token' => $token,
    ]);
  }
}
