<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateMissingTeams extends Command
{
  protected $signature = 'users:create-missing-teams';
  protected $description = 'Create personal teams for users who do not have any team membership';

  public function handle(): int
  {
    $usersWithoutTeams = User::whereDoesntHave('teams')->get();

    if ($usersWithoutTeams->isEmpty()) {
      $this->info('All users already have teams.');
      return Command::SUCCESS;
    }

    $this->info("Found {$usersWithoutTeams->count()} user(s) without teams.");

    $bar = $this->output->createProgressBar($usersWithoutTeams->count());
    $bar->start();

    foreach ($usersWithoutTeams as $user) {
      $team = Team::create([
        'name' => "{$user->name}'s Team",
        'slug' => Str::slug($user->name) . '-' . Str::random(6),
        'owner_id' => $user->id,
      ]);

      $team->members()->attach($user->id, ['role' => 'owner']);
      $bar->advance();
    }

    $bar->finish();
    $this->newLine();
    $this->info('Personal teams created successfully!');

    return Command::SUCCESS;
  }
}
