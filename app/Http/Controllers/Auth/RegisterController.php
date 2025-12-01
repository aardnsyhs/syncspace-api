<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\EmailVerification;
use App\Models\User;
use App\Notifications\SendOTPNotification;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
  public function __invoke(RegisterRequest $request): JsonResponse
  {
    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => $request->password,
    ]);

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
      'message' => 'Registration successful. Please check your email for verification code.',
      'data' => [
        'email' => $user->email,
        'requires_verification' => true,
      ]
    ], 201);
  }
}
