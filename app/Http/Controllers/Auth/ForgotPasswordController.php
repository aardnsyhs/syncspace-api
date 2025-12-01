<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
  public function __invoke(ForgotPasswordRequest $request): JsonResponse
  {
    $status = Password::sendResetLink(
      $request->only('email')
    );

    if ($status === Password::RESET_LINK_SENT) {
      return response()->json([
        'message' => 'Password reset link has been sent to your email address.',
      ]);
    }

    return response()->json([
      'message' => 'Unable to send password reset link. Please try again later.',
    ], 500);
  }
}
