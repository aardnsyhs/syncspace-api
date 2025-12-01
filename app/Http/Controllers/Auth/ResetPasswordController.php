<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
  public function __invoke(ResetPasswordRequest $request): JsonResponse
  {
    $status = Password::reset(
      $request->only('email', 'password', 'password_confirmation', 'token'),
      function ($user, $password) {
        $user->forceFill([
          'password' => Hash::make($password)
        ])->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));
      }
    );

    if ($status === Password::PASSWORD_RESET) {
      return response()->json([
        'message' => 'Your password has been reset successfully.',
      ]);
    }

    return response()->json([
      'message' => 'Unable to reset password. The link may have expired or is invalid.',
      'errors' => [
        'token' => ['The password reset token is invalid or has expired.']
      ]
    ], 422);
  }
}
