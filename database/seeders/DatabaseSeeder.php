<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Card;
use App\Models\Column;
use App\Models\Comment;
use App\Models\Label;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    // Create demo users (UserObserver auto-creates personal teams)
    $owner = User::factory()->create([
      'name' => 'John Doe (Owner)',
      'email' => 'owner@example.com',
      'password' => bcrypt('password'),
    ]);

    $admin = User::factory()->create([
      'name' => 'Jane Smith (Admin)',
      'email' => 'admin@example.com',
      'password' => bcrypt('password'),
    ]);

    $member = User::factory()->create([
      'name' => 'Bob Wilson (Member)',
      'email' => 'member@example.com',
      'password' => bcrypt('password'),
    ]);

    $viewer = User::factory()->create([
      'name' => 'Alice Brown (Viewer)',
      'email' => 'viewer@example.com',
      'password' => bcrypt('password'),
    ]);

    // Create a shared team (separate from personal teams)
    $team = Team::factory()->create([
      'name' => 'Acme Corp',
      'slug' => 'acme-corp',
      'owner_id' => $owner->id,
    ]);

    // Add members with different roles to shared team
    $team->members()->attach($owner->id, ['role' => 'owner']);
    $team->members()->attach($admin->id, ['role' => 'admin']);
    $team->members()->attach($member->id, ['role' => 'member']);
    $team->members()->attach($viewer->id, ['role' => 'viewer']);

    // Create a board
    $board = Board::factory()->create([
      'team_id' => $team->id,
      'name' => 'Project Alpha',
      'description' => 'Main project board for tracking tasks',
      'color' => '#3b82f6',
    ]);

    // Create labels for the board (labels are now board-scoped)
    $labels = collect([
      ['name' => 'Bug', 'color' => '#ef4444'],
      ['name' => 'Feature', 'color' => '#3b82f6'],
      ['name' => 'Enhancement', 'color' => '#8b5cf6'],
      ['name' => 'Urgent', 'color' => '#f97316'],
    ])->map(fn($label) => Label::create([...$label, 'board_id' => $board->id]));

    // Create columns
    $columnNames = ['To Do', 'In Progress', 'Review', 'Done'];
    $columns = collect($columnNames)->map(fn($name, $index) => Column::create([
      'board_id' => $board->id,
      'name' => $name,
      'position' => $index,
    ]));

    $allMembers = [$owner, $admin, $member, $viewer];

    // Create cards in each column
    $columns->each(function ($column, $colIndex) use ($allMembers, $labels) {
      $cardCount = match ($colIndex) {
        0 => 4,
        1 => 2,
        2 => 1,
        3 => 3,
        default => 0,
      };

      for ($i = 0; $i < $cardCount; $i++) {
        $card = Card::factory()->create([
          'column_id' => $column->id,
          'position' => $i,
          'assignee_id' => fake()->optional(0.7)->randomElement(
            array_map(fn($u) => $u->id, $allMembers)
          ),
        ]);

        $card->labels()->attach(
          $labels->random(rand(0, 2))->pluck('id')
        );

        if (fake()->boolean(60)) {
          Comment::factory(rand(1, 3))->create([
            'card_id' => $card->id,
            'user_id' => fake()->randomElement(
              array_map(fn($u) => $u->id, $allMembers)
            ),
          ]);
        }
      }
    });

    // Create second board
    Board::factory()->create([
      'team_id' => $team->id,
      'name' => 'Marketing Campaign',
      'color' => '#10b981',
    ]);

    $this->command->info('Demo users created:');
    $this->command->table(
      ['Email', 'Password', 'Role'],
      [
        ['owner@example.com', 'password', 'Owner'],
        ['admin@example.com', 'password', 'Admin'],
        ['member@example.com', 'password', 'Member'],
        ['viewer@example.com', 'password', 'Viewer'],
      ]
    );
  }
}
