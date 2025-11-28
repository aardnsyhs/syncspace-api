<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
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
    // Return JSON untuk API routes
    $exceptions->render(function (NotFoundHttpException $e, Request $request) {
      if ($request->is('api/*')) {
        return response()->json([
          'message' => 'Resource not found.',
        ], 404);
      }
    });
  })->create();
