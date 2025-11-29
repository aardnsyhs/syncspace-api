<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
  /**
   * Update user profile
   */
  public function update(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'name' => ['sometimes', 'string', 'max:255'],
      'avatar_url' => ['sometimes', 'nullable', 'url', 'max:500'],
    ]);

    $user = $request->user();
    $user->update($validated);

    return response()->json([
      'message' => 'Profile updated successfully.',
      'data' => new UserResource($user->fresh()),
    ]);
  }

  /**
   * Update user password
   */
  public function updatePassword(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'current_password' => ['required', 'string'],
      'password' => ['required', 'string', Password::defaults(), 'confirmed'],
    ]);

    $user = $request->user();

    if (!Hash::check($validated['current_password'], $user->password)) {
      return response()->json([
        'message' => 'The current password is incorrect.',
        'errors' => [
          'current_password' => ['The current password is incorrect.'],
        ],
      ], 422);
    }

    $user->update([
      'password' => Hash::make($validated['password']),
    ]);

    return response()->json([
      'message' => 'Password updated successfully.',
    ]);
  }
}
