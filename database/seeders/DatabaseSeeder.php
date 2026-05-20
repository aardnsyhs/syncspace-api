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
    // -----------------------------------------------------------------------
    // Demo Users
    // Four users covering every role tier so buyers can test permissions
    // immediately after setup. Credentials are printed to the console below.
    // -----------------------------------------------------------------------
    $owner = User::factory()->create([
      'name'     => 'Demo Owner',
      'email'    => 'owner@demo.com',
      'password' => bcrypt('password'),
    ]);

    $admin = User::factory()->create([
      'name'     => 'Demo Admin',
      'email'    => 'admin@demo.com',
      'password' => bcrypt('password'),
    ]);

    $member = User::factory()->create([
      'name'     => 'Demo Member',
      'email'    => 'member@demo.com',
      'password' => bcrypt('password'),
    ]);

    $viewer = User::factory()->create([
      'name'     => 'Demo Viewer',
      'email'    => 'viewer@demo.com',
      'password' => bcrypt('password'),
    ]);

    // -----------------------------------------------------------------------
    // Demo Workspace (Team)
    // -----------------------------------------------------------------------
    $team = Team::factory()->create([
      'name'     => 'Demo Workspace',
      'slug'     => 'demo-workspace',
      'owner_id' => $owner->id,
    ]);

    $team->members()->attach($owner->id,  ['role' => 'owner']);
    $team->members()->attach($admin->id,  ['role' => 'admin']);
    $team->members()->attach($member->id, ['role' => 'member']);
    $team->members()->attach($viewer->id, ['role' => 'viewer']);

    // -----------------------------------------------------------------------
    // Demo Board — uses the configurable default columns from config/board.php
    // -----------------------------------------------------------------------
    $board = Board::factory()->create([
      'team_id'     => $team->id,
      'name'        => 'Sample Project Board',
      'description' => 'A demo board to explore the boilerplate features.',
      'color'       => '#3b82f6',
    ]);

    // Labels — generic enough to apply to any project type
    $labels = collect([
      ['name' => 'Bug',         'color' => '#ef4444'],
      ['name' => 'Feature',     'color' => '#3b82f6'],
      ['name' => 'Enhancement', 'color' => '#8b5cf6'],
      ['name' => 'Urgent',      'color' => '#f97316'],
    ])->map(fn($label) => Label::create([...$label, 'board_id' => $board->id]));

    // Columns — pulled from config so they stay in sync with BoardController
    $columnNames = config('board.default_columns', ['To Do', 'In Progress', 'Review', 'Done']);
    $columns = collect($columnNames)->map(fn($name, $index) => Column::create([
      'board_id' => $board->id,
      'name'     => $name,
      'position' => $index,
    ]));

    $allMembers = [$owner, $admin, $member, $viewer];

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
          'column_id'   => $column->id,
          'position'    => $i,
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

    // A second board to demonstrate multi-board workspaces
    Board::factory()->create([
      'team_id' => $team->id,
      'name'    => 'Second Sample Board',
      'color'   => '#10b981',
    ]);

    // -----------------------------------------------------------------------
    // Console output — credentials table for quick reference after seeding
    // -----------------------------------------------------------------------
    $this->command->info('');
    $this->command->info('✅ Demo data seeded successfully.');
    $this->command->info('');
    $this->command->table(
      ['Role', 'Email', 'Password'],
      [
        ['Owner',  'owner@demo.com',  'password'],
        ['Admin',  'admin@demo.com',  'password'],
        ['Member', 'member@demo.com', 'password'],
        ['Viewer', 'viewer@demo.com', 'password'],
      ]
    );
  }
}
