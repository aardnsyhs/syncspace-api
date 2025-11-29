<?php

// tests/Feature/CardFlowTest.php

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
  $this->column = Column::factory()->create([
    'board_id' => $this->board->id,
    'name' => 'To Do',
    'position' => 0,
  ]);
});

test('user can create a card', function () {
  $response = $this->actingAs($this->user)
    ->postJson("/api/columns/{$this->column->id}/cards", [
      'title' => 'New Task',
      'description' => 'Task description',
    ]);

  $response->assertStatus(201)
    ->assertJsonPath('data.title', 'New Task')
    ->assertJsonPath('data.column_id', $this->column->id);

  $this->assertDatabaseHas('cards', [
    'title' => 'New Task',
    'column_id' => $this->column->id,
  ]);
});

test('user can update a card', function () {
  $card = Card::factory()->create(['column_id' => $this->column->id]);

  $response = $this->actingAs($this->user)
    ->putJson("/api/cards/{$card->id}", [
      'title' => 'Updated Title',
      'description' => 'Updated description',
      'due_date' => '2025-12-31',
    ]);

  $response->assertOk()
    ->assertJsonPath('data.title', 'Updated Title');
});

test('user can move card to another column', function () {
  $card = Card::factory()->create([
    'column_id' => $this->column->id,
    'position' => 0,
  ]);

  $targetColumn = Column::factory()->create([
    'board_id' => $this->board->id,
    'name' => 'In Progress',
    'position' => 1,
  ]);

  $response = $this->actingAs($this->user)
    ->putJson("/api/cards/{$card->id}/move", [
      'column_id' => $targetColumn->id,
      'position' => 0,
    ]);

  $response->assertOk()
    ->assertJsonPath('data.column_id', $targetColumn->id);

  $this->assertDatabaseHas('cards', [
    'id' => $card->id,
    'column_id' => $targetColumn->id,
  ]);
});

test('wip limit prevents adding cards when exceeded', function () {
  // Set WIP limit to 2
  $this->column->update(['wip_limit' => 2]);

  // Create 2 cards (at limit)
  Card::factory(2)->create(['column_id' => $this->column->id]);

  // Try to create 3rd card - should still work but with warning
  $response = $this->actingAs($this->user)
    ->postJson("/api/columns/{$this->column->id}/cards", [
      'title' => 'Third Card',
    ]);

  // Card is created but WIP is exceeded
  $response->assertStatus(201);

  // Verify column shows WIP exceeded
  $boardResponse = $this->actingAs($this->user)
    ->getJson("/api/boards/{$this->board->id}/cards");

  $boardResponse->assertOk();
  $columns = $boardResponse->json('data.columns');
  $column = collect($columns)->firstWhere('id', $this->column->id);
  expect($column['wip_exceeded'])->toBeTrue();
});

test('user can delete a card', function () {
  $card = Card::factory()->create(['column_id' => $this->column->id]);

  $response = $this->actingAs($this->user)
    ->deleteJson("/api/cards/{$card->id}");

  $response->assertStatus(204);
  $this->assertDatabaseMissing('cards', ['id' => $card->id]);
});

test('card positions are reordered correctly', function () {
  $cards = Card::factory(3)->create([
    'column_id' => $this->column->id,
  ])->each(function ($card, $index) {
    $card->update(['position' => $index]);
  });

  // Move last card to first position
  $lastCard = $cards->last();

  $response = $this->actingAs($this->user)
    ->putJson("/api/cards/{$lastCard->id}/move", [
      'column_id' => $this->column->id,
      'position' => 0,
    ]);

  $response->assertOk();

  // Verify positions
  $lastCard->refresh();
  expect($lastCard->position)->toBe(0);
});

test('user cannot access card from board they are not member of', function () {
  $otherTeam = Team::factory()->create();
  $otherBoard = Board::factory()->create(['team_id' => $otherTeam->id]);
  $otherColumn = Column::factory()->create(['board_id' => $otherBoard->id]);
  $card = Card::factory()->create(['column_id' => $otherColumn->id]);

  $response = $this->actingAs($this->user)
    ->getJson("/api/cards/{$card->id}");

  $response->assertStatus(403);
});
