<?php

// app/Http/Middleware/LogSecurityEvents.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogSecurityEvents
{
  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next): Response
  {
    $response = $next($request);

    // Log failed authentication attempts
    if ($this->isAuthEndpoint($request) && $response->getStatusCode() === 422) {
      Log::channel('security')->warning('Failed login attempt', [
        'ip' => $request->ip(),
        'email' => $request->input('email'),
        'user_agent' => $request->userAgent(),
        'timestamp' => now()->toIso8601String(),
      ]);
    }

    // Log unauthorized access attempts
    if ($response->getStatusCode() === 403) {
      Log::channel('security')->warning('Unauthorized access attempt', [
        'ip' => $request->ip(),
        'user_id' => $request->user()?->id,
        'path' => $request->path(),
        'method' => $request->method(),
        'user_agent' => $request->userAgent(),
        'timestamp' => now()->toIso8601String(),
      ]);
    }

    // Log rate limit exceeded
    if ($response->getStatusCode() === 429) {
      Log::channel('security')->warning('Rate limit exceeded', [
        'ip' => $request->ip(),
        'path' => $request->path(),
        'user_agent' => $request->userAgent(),
        'timestamp' => now()->toIso8601String(),
      ]);
    }

    return $response;
  }

  /**
   * Check if request is to an auth endpoint.
   */
  private function isAuthEndpoint(Request $request): bool
  {
    return in_array($request->path(), ['api/login', 'api/register']);
  }
}
