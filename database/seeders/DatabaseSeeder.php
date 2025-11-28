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
    // Create demo user
    $user = User::factory()->create([
      'name' => 'John Doe',
      'email' => 'john@example.com',
      'password' => bcrypt('password'),
    ]);

    // Create a team with the user as owner
    $team = Team::factory()->create([
      'name' => 'Acme Corp',
      'slug' => 'acme-corp',
      'owner_id' => $user->id,
    ]);

    // Add user as team member with owner role
    $team->members()->attach($user->id, ['role' => 'owner']);

    // Create additional team members
    $members = User::factory(3)->create();
    foreach ($members as $member) {
      $team->members()->attach($member->id, ['role' => 'member']);
    }

    // Create labels for the team
    $labels = collect([
      ['name' => 'Bug', 'color' => '#ef4444'],
      ['name' => 'Feature', 'color' => '#3b82f6'],
      ['name' => 'Enhancement', 'color' => '#8b5cf6'],
      ['name' => 'Urgent', 'color' => '#f97316'],
    ])->map(fn($label) => Label::create([...$label, 'team_id' => $team->id]));

    // Create a board
    $board = Board::factory()->create([
      'team_id' => $team->id,
      'name' => 'Project Alpha',
      'description' => 'Main project board for tracking tasks',
      'color' => '#3b82f6',
    ]);

    // Create columns
    $columnNames = ['To Do', 'In Progress', 'Review', 'Done'];
    $columns = collect($columnNames)->map(fn($name, $index) => Column::create([
      'board_id' => $board->id,
      'name' => $name,
      'position' => $index,
    ]));

    // Create cards in each column
    $columns->each(function ($column, $colIndex) use ($user, $members, $labels) {
      $cardCount = match ($colIndex) {
        0 => 4,  // To Do
        1 => 2,  // In Progress
        2 => 1,  // Review
        3 => 3,  // Done
        default => 0,
      };

      for ($i = 0; $i < $cardCount; $i++) {
        $card = Card::factory()->create([
          'column_id' => $column->id,
          'position' => $i,
          'assignee_id' => fake()->optional(0.7)->randomElement(
            [$user->id, ...$members->pluck('id')->toArray()]
          ),
        ]);

        // Attach random labels
        $card->labels()->attach(
          $labels->random(rand(0, 2))->pluck('id')
        );

        // Add some comments
        if (fake()->boolean(60)) {
          Comment::factory(rand(1, 3))->create([
            'card_id' => $card->id,
            'user_id' => fake()->randomElement(
              [$user->id, ...$members->pluck('id')->toArray()]
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
  }
}
