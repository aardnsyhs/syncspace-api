<?php

namespace App\Observers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;

class UserObserver
{
  /**
   * Handle the User "created" event.
   * Automatically create a personal team for new users.
   */
  public function created(User $user): void
  {
    // Create personal team
    $team = Team::create([
      'name' => $user->name . "'s Team",
      'slug' => Str::slug($user->name) . '-' . Str::random(6),
      'owner_id' => $user->id,
    ]);

    // Add user as owner
    $team->members()->attach($user->id, ['role' => 'owner']);
  }
}
