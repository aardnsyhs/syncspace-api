<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
  /**
   * Get user notifications
   */
  public function index(Request $request): JsonResponse
  {
    $user = $request->user();
    $limit = $request->input('limit', 20);

    $notifications = Notification::forUser($user->id)
      ->orderBy('created_at', 'desc')
      ->limit($limit)
      ->get()
      ->map(function ($notification) {
        return [
          'id' => (string) $notification->id,
          'type' => $notification->type,
          'title' => $notification->title,
          'message' => $notification->message,
          'data' => $notification->data,
          'read' => $notification->isRead(),
          'created_at' => $notification->created_at->toISOString(),
        ];
      });

    $unreadCount = Notification::forUser($user->id)->unread()->count();

    return response()->json([
      'data' => $notifications,
      'meta' => [
        'unread_count' => $unreadCount,
      ],
    ]);
  }

  /**
   * Mark notification as read
   */
  public function markAsRead(Request $request, Notification $notification): JsonResponse
  {
    // Ensure user owns this notification
    if ($notification->user_id !== $request->user()->id) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    $notification->markAsRead();

    return response()->json([
      'message' => 'Notification marked as read',
    ]);
  }

  /**
   * Mark all notifications as read
   */
  public function markAllAsRead(Request $request): JsonResponse
  {
    Notification::forUser($request->user()->id)
      ->unread()
      ->update(['read_at' => now()]);

    return response()->json([
      'message' => 'All notifications marked as read',
    ]);
  }

  /**
   * Delete a notification
   */
  public function destroy(Request $request, Notification $notification): JsonResponse
  {
    // Ensure user owns this notification
    if ($notification->user_id !== $request->user()->id) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    $notification->delete();

    return response()->json(null, 204);
  }

  /**
   * Clear all notifications
   */
  public function clearAll(Request $request): JsonResponse
  {
    Notification::forUser($request->user()->id)->delete();

    return response()->json([
      'message' => 'All notifications cleared',
    ]);
  }
}
