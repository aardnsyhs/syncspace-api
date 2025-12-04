<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
  /**
   * Handle Google OAuth callback - exchange code for user info
   */
  public function callback(Request $request): JsonResponse
  {
    $request->validate([
      'code' => 'required|string',
    ]);

    try {
      $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'code' => $request->code,
        'client_id' => config('services.google.client_id'),
        'client_secret' => config('services.google.client_secret'),
        'redirect_uri' => config('services.google.redirect'),
        'grant_type' => 'authorization_code',
      ]);

      if (!$tokenResponse->successful()) {
        return response()->json([
          'message' => 'Failed to exchange authorization code.',
        ], 401);
      }

      $accessToken = $tokenResponse->json('access_token');

      $userResponse = Http::withToken($accessToken)
        ->get('https://www.googleapis.com/oauth2/v2/userinfo');

      if (!$userResponse->successful()) {
        return response()->json([
          'message' => 'Failed to get user information from Google.',
        ], 401);
      }

      $googleUser = $userResponse->json();
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Google authentication failed.',
      ], 401);
    }

    $user = User::where('email', $googleUser['email'])->first();

    if ($user) {
      if (!$user->google_id) {
        $user->update([
          'google_id' => $googleUser['id'],
          'avatar_url' => $user->avatar_url ?? $googleUser['picture'] ?? null,
        ]);
      }
    } else {
      $user = User::create([
        'name' => $googleUser['name'],
        'email' => $googleUser['email'],
        'google_id' => $googleUser['id'],
        'avatar_url' => $googleUser['picture'] ?? null,
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
