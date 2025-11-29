<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Cross-Origin Resource Sharing (CORS) Configuration
  |--------------------------------------------------------------------------
  |
  | SECURITY: Only allow specific origins, never use '*' with credentials.
  | Add production domain to FRONTEND_URL in .env.production
  |
  */

  'paths' => ['api/*', 'sanctum/csrf-cookie', 'broadcasting/auth'],

  'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

  'allowed_origins' => array_filter([
    env('FRONTEND_URL', 'http://localhost:5173'),
    env('FRONTEND_URL_PROD'), // Add production URL in .env
  ]),

  'allowed_origins_patterns' => [],

  'allowed_headers' => [
    'Accept',
    'Authorization',
    'Content-Type',
    'X-Requested-With',
    'X-XSRF-TOKEN',
    'X-Socket-ID',
  ],

  'exposed_headers' => [],

  'max_age' => 7200, // 2 hours cache for preflight

  'supports_credentials' => true,

];
