<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
  public function run(): void
  {
    $users = User::all();

    foreach ($users as $user) {
      // Create some sample notifications for each user
      Notification::create([
        'user_id' => $user->id,
        'type' => 'card_assigned',
        'title' => 'Card Assigned',
        'message' => 'You were assigned to "Implement login page"',
        'data' => ['card_id' => 1, 'board_id' => 1],
        'read_at' => null,
        'created_at' => now()->subMinutes(5),
      ]);

      Notification::create([
        'user_id' => $user->id,
        'type' => 'comment',
        'title' => 'New Comment',
        'message' => 'John commented on "API Integration": Great progress!',
        'data' => ['card_id' => 2, 'board_id' => 1],
        'read_at' => null,
        'created_at' => now()->subMinutes(30),
      ]);

      Notification::create([
        'user_id' => $user->id,
        'type' => 'due_soon',
        'title' => 'Due Soon',
        'message' => '"Design review" is due tomorrow',
        'data' => ['card_id' => 3, 'board_id' => 1],
        'read_at' => now()->subHours(1),
        'created_at' => now()->subHours(2),
      ]);

      Notification::create([
        'user_id' => $user->id,
        'type' => 'card_moved',
        'title' => 'Card Moved',
        'message' => 'Sarah moved "Setup CI/CD" from In Progress to Review',
        'data' => ['card_id' => 4, 'board_id' => 1],
        'read_at' => now()->subHours(3),
        'created_at' => now()->subHours(4),
      ]);
    }
  }
}
