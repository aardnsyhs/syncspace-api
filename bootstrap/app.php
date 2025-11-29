<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    channels: __DIR__ . '/../routes/channels.php',
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware): void {
    // Sanctum stateful middleware untuk SPA authentication
    $middleware->statefulApi();

    // CORS configuration
    $middleware->validateCsrfTokens(except: [
      'api/*',
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions): void {
    // Return JSON 401 for unauthenticated API requests (instead of redirect to login)
    $exceptions->render(function (AuthenticationException $e, Request $request) {
      // Always return JSON for API requests - check both path patterns and Accept header
      if ($request->is('api/*') || $request->is('*/api/*') || $request->expectsJson() || $request->wantsJson()) {
        return response()->json([
          'message' => 'Unauthenticated.',
        ], 401);
      }
    });

    // Return JSON untuk API routes
    $exceptions->render(function (NotFoundHttpException $e, Request $request) {
      if ($request->is('api/*') || $request->is('*/api/*') || $request->expectsJson()) {
        return response()->json([
          'message' => 'Resource not found.',
        ], 404);
      }
    });
  })->create();
