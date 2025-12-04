<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResendOTPController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\VerifyOTPController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\BoardAnalyticsController;
use App\Http\Controllers\BoardCardController;
use App\Http\Controllers\BoardLabelController;
use App\Http\Controllers\BoardTemplateController;
use App\Http\Controllers\PublicBoardController;
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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn() => response()->json(['status' => 'ok']));

Route::get('/health/app', function () {
  try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    return response()->json([
      'status' => 'ok',
      'database' => 'connected',
      'timestamp' => now()->toIso8601String(),
    ]);
  } catch (\Exception $e) {
    return response()->json([
      'status' => 'error',
      'database' => 'disconnected',
      'timestamp' => now()->toIso8601String(),
    ], 503);
  }
});

Route::middleware('throttle:public-board')
  ->get('/public/boards/{token}', [PublicBoardController::class, 'show']);

Route::middleware('throttle:auth')->group(function () {
  Route::post('/login', LoginController::class);
});

Route::middleware('throttle:register')->group(function () {
  Route::post('/register', RegisterController::class);
});

Route::middleware('throttle:auth')->group(function () {
  Route::post('/forgot-password', ForgotPasswordController::class);
  Route::post('/reset-password', ResetPasswordController::class);
  Route::post('/verify-otp', VerifyOTPController::class);
  Route::post('/resend-otp', ResendOTPController::class);

  Route::post('/auth/google/callback', [GoogleAuthController::class, 'callback']);
});

Route::middleware('auth:sanctum')->group(function () {
  Route::post('/logout', LogoutController::class);
  Route::get('/user', UserController::class);

  Route::put('/user/profile', [ProfileController::class, 'update']);
  Route::put('/user/password', [ProfileController::class, 'updatePassword']);
  Route::put('/user/notification-preferences', [ProfileController::class, 'updateNotificationPreferences']);
  Route::post('/user/avatar', [ProfileController::class, 'uploadAvatar']);

  Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
  Route::get('/dashboard/activities', [DashboardController::class, 'activities']);
  Route::get('/dashboard/my-cards', [DashboardController::class, 'myCards']);

  Route::get('/notifications', [NotificationController::class, 'index']);
  Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
  Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
  Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);
  Route::delete('/notifications', [NotificationController::class, 'clearAll']);

  Route::post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
    return Broadcast::auth($request);
  });

  Route::apiResource('teams', TeamController::class);
  Route::get('/teams/{team}/members', [TeamMemberController::class, 'index']);
  Route::post('/teams/{team}/members', [TeamMemberController::class, 'store']);
  Route::patch('/teams/{team}/members/{user}', [TeamMemberController::class, 'update']);
  Route::delete('/teams/{team}/members/{user}', [TeamMemberController::class, 'destroy']);

  Route::get('/teams/{team}/boards', [BoardController::class, 'index']);
  Route::post('/teams/{team}/boards', [BoardController::class, 'store']);

  Route::get('/boards/{board}', [BoardController::class, 'show']);
  Route::put('/boards/{board}', [BoardController::class, 'update']);
  Route::delete('/boards/{board}', [BoardController::class, 'destroy']);
  Route::get('/boards/{board}/activities', [ActivityController::class, 'index']);

  Route::get('/boards/{board}/analytics/summary', [BoardAnalyticsController::class, 'summary']);
  Route::get('/boards/{board}/analytics/throughput', [BoardAnalyticsController::class, 'throughput']);
  Route::get('/boards/{board}/analytics/cumulative-flow', [BoardAnalyticsController::class, 'cumulativeFlow']);
  Route::get('/boards/{board}/analytics/assignees', [BoardAnalyticsController::class, 'assignees']);

  Route::post('/boards/{board}/columns', [ColumnController::class, 'store']);
  Route::put('/columns/{column}', [ColumnController::class, 'update']);
  Route::delete('/columns/{column}', [ColumnController::class, 'destroy']);
  Route::put('/columns/{column}/move', [ColumnController::class, 'move']);
  Route::patch('/columns/{column}/wip-limit', [ColumnController::class, 'updateWipLimit']);

  Route::get('/boards/{board}/cards', [BoardCardController::class, 'index']);

  Route::post('/columns/{column}/cards', [CardController::class, 'store']);
  Route::get('/cards/{card}', [CardController::class, 'show']);
  Route::put('/cards/{card}', [CardController::class, 'update']);
  Route::delete('/cards/{card}', [CardController::class, 'destroy']);
  Route::put('/cards/{card}/move', [CardController::class, 'move']);

  Route::get('/cards/{card}/comments', [CommentController::class, 'index']);
  Route::post('/cards/{card}/comments', [CommentController::class, 'store']);
  Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

  Route::get('/boards/{board}/labels', [BoardLabelController::class, 'index']);
  Route::post('/boards/{board}/labels', [BoardLabelController::class, 'store']);
  Route::patch('/boards/{board}/labels/{label}', [BoardLabelController::class, 'update']);
  Route::delete('/boards/{board}/labels/{label}', [BoardLabelController::class, 'destroy']);

  Route::post('/cards/{card}/labels', [CardLabelController::class, 'store']);
  Route::delete('/cards/{card}/labels/{label}', [CardLabelController::class, 'destroy']);

  Route::get('/cards/{card}/checklists', [ChecklistController::class, 'index']);
  Route::post('/cards/{card}/checklists', [ChecklistController::class, 'store']);
  Route::patch('/checklists/{checklist}', [ChecklistController::class, 'update']);
  Route::delete('/checklists/{checklist}', [ChecklistController::class, 'destroy']);

  Route::post('/checklists/{checklist}/items', [ChecklistItemController::class, 'store']);
  Route::patch('/checklist-items/{checklistItem:id}', [ChecklistItemController::class, 'update']);
  Route::delete('/checklist-items/{checklistItem:id}', [ChecklistItemController::class, 'destroy']);

  Route::get('/cards/{card}/attachments', [AttachmentController::class, 'index']);
  Route::post('/cards/{card}/attachments', [AttachmentController::class, 'store']);
  Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download']);
  Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy']);

  Route::get('/board-templates', [BoardTemplateController::class, 'index']);
  Route::get('/board-templates/{template}', [BoardTemplateController::class, 'show']);
  Route::post('/teams/{team}/board-templates', [BoardTemplateController::class, 'store']);
  Route::delete('/board-templates/{template}', [BoardTemplateController::class, 'destroy']);
  Route::post('/teams/{team}/boards/from-template', [BoardTemplateController::class, 'createBoardFromTemplate']);

  Route::post('/boards/{board}/public/enable', [PublicBoardController::class, 'enable']);
  Route::post('/boards/{board}/public/disable', [PublicBoardController::class, 'disable']);
  Route::post('/boards/{board}/public/regenerate', [PublicBoardController::class, 'regenerate']);
});
