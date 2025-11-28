<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMemberController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/health', fn() => response()->json(['status' => 'ok']));

/*
|--------------------------------------------------------------------------
| Auth Routes (Guest)
|--------------------------------------------------------------------------
*/

Route::post('/register', RegisterController::class);
Route::post('/login', LoginController::class);

/*
|--------------------------------------------------------------------------
| Protected Routes (Auth Required)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
  // Auth
  Route::post('/logout', LogoutController::class);
  Route::get('/user', UserController::class);

  // Teams
  Route::apiResource('teams', TeamController::class);
  Route::get('/teams/{team}/members', [TeamMemberController::class, 'index']);
  Route::post('/teams/{team}/members', [TeamMemberController::class, 'store']);
  Route::delete('/teams/{team}/members/{user}', [TeamMemberController::class, 'destroy']);

  // Boards (nested under team for listing/creating)
  Route::get('/teams/{team}/boards', [BoardController::class, 'index']);
  Route::post('/teams/{team}/boards', [BoardController::class, 'store']);

  // Boards (direct access for show/update/delete)
  Route::get('/boards/{board}', [BoardController::class, 'show']);
  Route::put('/boards/{board}', [BoardController::class, 'update']);
  Route::delete('/boards/{board}', [BoardController::class, 'destroy']);

  // Columns
  Route::post('/boards/{board}/columns', [ColumnController::class, 'store']);
  Route::put('/columns/{column}', [ColumnController::class, 'update']);
  Route::delete('/columns/{column}', [ColumnController::class, 'destroy']);
  Route::put('/columns/{column}/move', [ColumnController::class, 'move']);

  // Cards
  Route::post('/columns/{column}/cards', [CardController::class, 'store']);
  Route::get('/cards/{card}', [CardController::class, 'show']);
  Route::put('/cards/{card}', [CardController::class, 'update']);
  Route::delete('/cards/{card}', [CardController::class, 'destroy']);
  Route::put('/cards/{card}/move', [CardController::class, 'move']);

  // Comments
  Route::get('/cards/{card}/comments', [CommentController::class, 'index']);
  Route::post('/cards/{card}/comments', [CommentController::class, 'store']);
  Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});
