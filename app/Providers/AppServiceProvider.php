<?php

// app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    // Register observers
    User::observe(UserObserver::class);

    $this->configureRateLimiting();
  }

  /**
   * Configure rate limiting for the application.
   */
  protected function configureRateLimiting(): void
  {
    // Auth endpoints - strict limiting
    RateLimiter::for('auth', function (Request $request) {
      return Limit::perMinute(5)->by(
        $request->input('email') . '|' . $request->ip()
      )->response(function () {
        return response()->json([
          'message' => 'Too many login attempts. Please try again in a minute.',
        ], 429);
      });
    });

    // Registration - moderate limiting
    RateLimiter::for('register', function (Request $request) {
      return Limit::perMinute(3)->by($request->ip())->response(function () {
        return response()->json([
          'message' => 'Too many registration attempts. Please try again later.',
        ], 429);
      });
    });

    // Public board access - prevent scraping
    RateLimiter::for('public-board', function (Request $request) {
      return Limit::perMinute(30)->by($request->ip())->response(function () {
        return response()->json([
          'message' => 'Too many requests. Please slow down.',
        ], 429);
      });
    });

    // API general - generous but limited
    RateLimiter::for('api', function (Request $request) {
      return Limit::perMinute(60)->by(
        $request->user()?->id ?: $request->ip()
      );
    });

    // File uploads - strict
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
