<?php

namespace App\Observers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;

class UserObserver
{

  public function created(User $user): void
  {
    
    $team = Team::create([
      'name' => $user->name . "'s Team",
      'slug' => Str::slug($user->name) . '-' . Str::random(6),
      'owner_id' => $user->id,
    ]);

    $team->members()->attach($user->id, ['role' => 'owner']);
  }
}
