<?php

return [

  'paths' => ['api/*', 'sanctum/csrf-cookie', 'broadcasting/auth'],

  'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

  'allowed_origins' => array_filter([
    env('FRONTEND_URL', 'http://localhost:5173'),
    env('FRONTEND_URL_PROD'),
    'http://localhost:5173',
    'http://syncspace.test:5173',
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

  'max_age' => 7200,

  'supports_credentials' => true,

];
