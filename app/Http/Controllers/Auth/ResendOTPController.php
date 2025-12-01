<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerification;
use App\Models\User;
use App\Notifications\SendOTPNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ResendOTPController extends Controller
{
  public function __invoke(Request $request): JsonResponse
  {
    $request->validate([
      'email' => ['required', 'email', 'exists:users,email'],
    ]);

    $user = User::where('email', $request->email)->first();

    if ($user->email_verified_at) {
      throw ValidationException::withMessages([
        'email' => ['This email is already verified.'],
      ]);
    }

    $lastVerification = EmailVerification::where('email', $request->email)
      ->latest()
      ->first();

    if ($lastVerification && !$lastVerification->isExpired() && $lastVerification->created_at->diffInSeconds(now()) < 60) {
      throw ValidationException::withMessages([
        'email' => ['Please wait before requesting a new code.'],
      ]);
    }

    $otp = EmailVerification::generateOTP();
    $expiryMinutes = 5;

    EmailVerification::updateOrCreate(
      ['email' => $user->email],
      [
        'otp' => $otp,
        'expires_at' => now()->addMinutes($expiryMinutes),
        'verified_at' => null,
      ]
    );

    $user->notify(new SendOTPNotification($otp, $expiryMinutes));

    return response()->json([
      'message' => 'Verification code has been resent to your email.',
    ]);
  }
}
