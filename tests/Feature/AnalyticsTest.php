<?php

// tests/Feature/AnalyticsTest.php

use App\Models\Board;
use App\Models\Card;
use App\Models\Column;
use App\Models\Team;
use App\Models\User;

beforeEach(function () {
  $this->user = User::factory()->create();
  $this->team = Team::factory()->create(['owner_id' => $this->user->id]);
  $this->team->members()->attach($this->user->id, ['role' => 'owner']);
  $this->board = Board::factory()->create(['team_id' => $this->team->id]);

  // Create columns
  $this->todoColumn = Column::factory()->create([
    'board_id' => $this->board->id,
    'name' => 'To Do',
    'position' => 0,
  ]);
  $this->doneColumn = Column::factory()->create([
    'board_id' => $this->board->id,
    'name' => 'Done',
    'position' => 1,
  ]);
});

test('user can get board analytics summary', function () {
  // Create some cards
  Card::factory(5)->create(['column_id' => $this->todoColumn->id]);
  Card::factory(3)->create(['column_id' => $this->doneColumn->id]);

  $response = $this->actingAs($this->user)
    ->getJson("/api/boards/{$this->board->id}/analytics/summary");

  $response->assertOk();
  expect($response->json('data.total_cards'))->toBe(8);
});

test('user can get cumulative flow data', function () {
  Card::factory(5)->create(['column_id' => $this->todoColumn->id]);
  Card::factory(3)->create(['column_id' => $this->doneColumn->id]);

  $response = $this->actingAs($this->user)
    ->getJson("/api/boards/{$this->board->id}/analytics/cumulative-flow?period=14");

  $response->assertOk()
    ->assertJsonStructure([
      'data' => [
        '*' => ['date', 'columns'],
      ],
    ]);
});

test('outsider cannot access board analytics', function () {
  $outsider = User::factory()->create();

  $response = $this->actingAs($outsider)
    ->getJson("/api/boards/{$this->board->id}/analytics/summary");

  $response->assertStatus(403);
});
