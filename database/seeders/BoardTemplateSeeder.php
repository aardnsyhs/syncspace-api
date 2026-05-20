<?php

namespace Database\Seeders;

use App\Models\BoardTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class BoardTemplateSeeder extends Seeder
{
  public function run(): void
  {
    // The system user owns global board templates.
    // Configure via SYSTEM_USER_EMAIL and SYSTEM_USER_NAME in your .env file,
    // or edit config/workspace.php to change the defaults.
    $systemUser = User::firstOrCreate(
      ['email' => config('workspace.system_user_email', 'system@example.com')],
      [
        'name'     => config('workspace.system_user_name', 'System'),
        'password' => bcrypt(str()->random(32)),
      ]
    );

    $templates = [
      [
        'name' => 'Product Roadmap',
        'description' => 'Plan and track product features from ideation to launch',
        'slug' => 'product-roadmap',
        'columns' => [
          ['name' => 'Backlog', 'wip_limit' => null],
          ['name' => 'Planning', 'wip_limit' => 5],
          ['name' => 'In Development', 'wip_limit' => 3],
          ['name' => 'Testing', 'wip_limit' => 3],
          ['name' => 'Released', 'wip_limit' => null],
        ],
      ],
      [
        'name' => 'Marketing Campaign',
        'description' => 'Organize marketing campaigns and content creation',
        'slug' => 'marketing-campaign',
        'columns' => [
          ['name' => 'Ideas', 'wip_limit' => null],
          ['name' => 'Planning', 'wip_limit' => 5],
          ['name' => 'In Progress', 'wip_limit' => 4],
          ['name' => 'Review', 'wip_limit' => 3],
          ['name' => 'Published', 'wip_limit' => null],
        ],
      ],
      [
        'name' => 'Personal Tasks',
        'description' => 'Simple board for personal task management',
        'slug' => 'personal-tasks',
        'columns' => [
          ['name' => 'To Do', 'wip_limit' => null],
          ['name' => 'Doing', 'wip_limit' => 3],
          ['name' => 'Done', 'wip_limit' => null],
        ],
      ],
      [
        'name' => 'Sprint Board',
        'description' => 'Agile sprint planning and tracking',
        'slug' => 'sprint-board',
        'columns' => [
          ['name' => 'Sprint Backlog', 'wip_limit' => null],
          ['name' => 'In Progress', 'wip_limit' => 4],
          ['name' => 'Code Review', 'wip_limit' => 3],
          ['name' => 'QA', 'wip_limit' => 3],
          ['name' => 'Done', 'wip_limit' => null],
        ],
      ],
      [
        'name' => 'Bug Tracking',
        'description' => 'Track and resolve bugs efficiently',
        'slug' => 'bug-tracking',
        'columns' => [
          ['name' => 'Reported', 'wip_limit' => null],
          ['name' => 'Triaged', 'wip_limit' => 10],
          ['name' => 'In Progress', 'wip_limit' => 3],
          ['name' => 'Fixed', 'wip_limit' => 5],
          ['name' => 'Verified', 'wip_limit' => null],
        ],
      ],
    ];

    foreach ($templates as $templateData) {
      $template = BoardTemplate::updateOrCreate(
        ['slug' => $templateData['slug']],
        [
          'team_id' => null,
          'name' => $templateData['name'],
          'description' => $templateData['description'],
          'visibility' => 'global',
          'created_by' => $systemUser->id,
        ]
      );

      $template->columns()->delete();

      foreach ($templateData['columns'] as $index => $columnData) {
        $template->columns()->create([
          'name' => $columnData['name'],
          'position' => $index,
          'wip_limit' => $columnData['wip_limit'],
        ]);
      }
    }
  }
}
