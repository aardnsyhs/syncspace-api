<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\BoardAnalyticsController;
use App\Http\Controllers\BoardCardController;
use App\Http\Controllers\BoardLabelController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CardLabelController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ChecklistItemController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMemberController;
use Illuminate\Support\Facades\Broadcast;
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

  // Broadcasting auth - manual route karena Broadcast::routes() tidak support prefix
  Route::post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
    return Broadcast::auth($request);
  });

  // Teams
  Route::apiResource('teams', TeamController::class);
  Route::get('/teams/{team}/members', [TeamMemberController::class, 'index']);
  Route::post('/teams/{team}/members', [TeamMemberController::class, 'store']);
  Route::patch('/teams/{team}/members/{user}', [TeamMemberController::class, 'update']);
  Route::delete('/teams/{team}/members/{user}', [TeamMemberController::class, 'destroy']);

  // Boards (nested under team for listing/creating)
  Route::get('/teams/{team}/boards', [BoardController::class, 'index']);
  Route::post('/teams/{team}/boards', [BoardController::class, 'store']);

  // Boards (direct access for show/update/delete)
  Route::get('/boards/{board}', [BoardController::class, 'show']);
  Route::put('/boards/{board}', [BoardController::class, 'update']);
  Route::delete('/boards/{board}', [BoardController::class, 'destroy']);
  Route::get('/boards/{board}/activities', [ActivityController::class, 'index']);

  // Board Analytics
  Route::get('/boards/{board}/analytics/summary', [BoardAnalyticsController::class, 'summary']);
  Route::get('/boards/{board}/analytics/throughput', [BoardAnalyticsController::class, 'throughput']);
  Route::get('/boards/{board}/analytics/cumulative-flow', [BoardAnalyticsController::class, 'cumulativeFlow']);
  Route::get('/boards/{board}/analytics/assignees', [BoardAnalyticsController::class, 'assignees']);

  // Columns
  Route::post('/boards/{board}/columns', [ColumnController::class, 'store']);
  Route::put('/columns/{column}', [ColumnController::class, 'update']);
  Route::delete('/columns/{column}', [ColumnController::class, 'destroy']);
  Route::put('/columns/{column}/move', [ColumnController::class, 'move']);
  Route::patch('/columns/{column}/wip-limit', [ColumnController::class, 'updateWipLimit']);

  // Board Cards (filtered)
  Route::get('/boards/{board}/cards', [BoardCardController::class, 'index']);

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

  // Board Labels
  Route::get('/boards/{board}/labels', [BoardLabelController::class, 'index']);
  Route::post('/boards/{board}/labels', [BoardLabelController::class, 'store']);
  Route::patch('/boards/{board}/labels/{label}', [BoardLabelController::class, 'update']);
  Route::delete('/boards/{board}/labels/{label}', [BoardLabelController::class, 'destroy']);

  // Card Labels
  Route::post('/cards/{card}/labels', [CardLabelController::class, 'store']);
  Route::delete('/cards/{card}/labels/{label}', [CardLabelController::class, 'destroy']);

  // Checklists
  Route::get('/cards/{card}/checklists', [ChecklistController::class, 'index']);
  Route::post('/cards/{card}/checklists', [ChecklistController::class, 'store']);
  Route::patch('/checklists/{checklist}', [ChecklistController::class, 'update']);
  Route::delete('/checklists/{checklist}', [ChecklistController::class, 'destroy']);

  // Checklist Items
  Route::post('/checklists/{checklist}/items', [ChecklistItemController::class, 'store']);
  Route::patch('/checklist-items/{checklistItem:id}', [ChecklistItemController::class, 'update']);
  Route::delete('/checklist-items/{checklistItem:id}', [ChecklistItemController::class, 'destroy']);

  // Attachments
  Route::get('/cards/{card}/attachments', [AttachmentController::class, 'index']);
  Route::post('/cards/{card}/attachments', [AttachmentController::class, 'store']);
  Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy']);
});
