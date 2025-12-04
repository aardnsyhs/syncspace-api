<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
  /**
   * Redirect to Google OAuth
   */
  public function redirect(): JsonResponse
  {
    $url = Socialite::driver('google')
      ->stateless()
      ->redirect()
      ->getTargetUrl();

    return response()->json(['url' => $url]);
  }

  /**
   * Handle Google OAuth callback
   */
  public function callback(Request $request): JsonResponse
  {
    $request->validate([
      'code' => 'required|string',
    ]);

    try {
      $googleUser = Socialite::driver('google')
        ->stateless()
        ->user();
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Invalid Google authentication code.',
      ], 401);
    }

    $user = User::where('email', $googleUser->getEmail())->first();

    if ($user) {
      if (!$user->google_id) {
        $user->update([
          'google_id' => $googleUser->getId(),
          'avatar_url' => $user->avatar_url ?? $googleUser->getAvatar(),
        ]);
      }
    } else {
      $user = User::create([
        'name' => $googleUser->getName(),
        'email' => $googleUser->getEmail(),
        'google_id' => $googleUser->getId(),
        'avatar_url' => $googleUser->getAvatar(),
        'password' => Hash::make(Str::random(24)),
        'email_verified_at' => now(),
      ]);
    }

    $token = $user->createToken('auth-token')->plainTextToken;

    return response()->json([
      'data' => new UserResource($user),
      'token' => $token,
    ]);
  }
}
