<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

  public function register(): void
  {
    
  }

  public function boot(): void
  {
    
    User::observe(UserObserver::class);

    $this->configureRateLimiting();
  }

  protected function configureRateLimiting(): void
  {
    
    RateLimiter::for('auth', function (Request $request) {
      return Limit::perMinute(5)->by(
        $request->input('email') . '|' . $request->ip()
      )->response(function () {
        return response()->json([
          'message' => 'Too many login attempts. Please try again in a minute.',
        ], 429);
      });
    });

    RateLimiter::for('register', function (Request $request) {
      return Limit::perMinute(3)->by($request->ip())->response(function () {
        return response()->json([
          'message' => 'Too many registration attempts. Please try again later.',
        ], 429);
      });
    });

    RateLimiter::for('public-board', function (Request $request) {
      return Limit::perMinute(30)->by($request->ip())->response(function () {
        return response()->json([
          'message' => 'Too many requests. Please slow down.',
        ], 429);
      });
    });

    RateLimiter::for('api', function (Request $request) {
      return Limit::perMinute(60)->by(
        $request->user()?->id ?: $request->ip()
      );
    });

    RateLimiter::for('uploads', function (Request $request) {
      return Limit::perMinute(10)->by(
        $request->user()?->id ?: $request->ip()
      )->response(function () {
        return response()->json([
          'message' => 'Upload limit reached. Please wait before uploading more files.',
        ], 429);
      });
    });
  }
}
