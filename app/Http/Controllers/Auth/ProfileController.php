<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{

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

  public function uploadAvatar(Request $request): JsonResponse
  {
    $request->validate([
      'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
    ]);

    $user = $request->user();

    if ($user->avatar_url) {
      $oldPath = str_replace('/storage/', '', parse_url($user->avatar_url, PHP_URL_PATH));
      if ($oldPath && Storage::disk('public')->exists($oldPath)) {
        Storage::disk('public')->delete($oldPath);
      }
    }

    $path = $request->file('avatar')->store('avatars', 'public');
    $avatarUrl = Storage::disk('public')->url($path);

    $user->update(['avatar_url' => $avatarUrl]);

    return response()->json([
      'message' => 'Avatar uploaded successfully.',
      'data' => new UserResource($user->fresh()),
    ]);
  }

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

  public function updateNotificationPreferences(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'email_notifications' => ['sometimes', 'boolean'],
      'desktop_notifications' => ['sometimes', 'boolean'],
    ]);

    $user = $request->user();
    $user->update($validated);

    return response()->json([
      'message' => 'Notification preferences updated successfully.',
      'data' => new UserResource($user->fresh()),
    ]);
  }
}
