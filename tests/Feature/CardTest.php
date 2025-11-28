<?php

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
  $this->column = Column::factory()->create(['board_id' => $this->board->id, 'position' => 0]);
});

test('user can create a card', function () {
  $response = $this->actingAs($this->user)
    ->postJson("/api/columns/{$this->column->id}/cards", [
      'title' => 'New Task',
      'description' => 'Task description',
    ]);

  $response->assertStatus(201)
    ->assertJsonPath('data.title', 'New Task')
    ->assertJsonPath('data.position', 0);
});

test('cards are positioned correctly', function () {
  // Create first card
  $this->actingAs($this->user)
    ->postJson("/api/columns/{$this->column->id}/cards", ['title' => 'Card 1']);

  // Create second card
  $response = $this->actingAs($this->user)
    ->postJson("/api/columns/{$this->column->id}/cards", ['title' => 'Card 2']);

  $response->assertJsonPath('data.position', 1);
});

test('user can move card within same column', function () {
  $card1 = Card::factory()->create(['column_id' => $this->column->id, 'position' => 0]);
  $card2 = Card::factory()->create(['column_id' => $this->column->id, 'position' => 1]);
  $card3 = Card::factory()->create(['column_id' => $this->column->id, 'position' => 2]);

  // Move card1 to position 2
  $response = $this->actingAs($this->user)
    ->putJson("/api/cards/{$card1->id}/move", [
      'column_id' => $this->column->id,
      'position' => 2,
    ]);

  $response->assertOk();

  // Verify positions
  expect($card1->fresh()->position)->toBe(2);
  expect($card2->fresh()->position)->toBe(0);
  expect($card3->fresh()->position)->toBe(1);
});

test('user can move card to different column', function () {
  $column2 = Column::factory()->create(['board_id' => $this->board->id, 'position' => 1]);

  $card = Card::factory()->create(['column_id' => $this->column->id, 'position' => 0]);

  $response = $this->actingAs($this->user)
    ->putJson("/api/cards/{$card->id}/move", [
      'column_id' => $column2->id,
      'position' => 0,
    ]);

  $response->assertOk()
    ->assertJsonPath('data.column_id', $column2->id)
    ->assertJsonPath('data.position', 0);
});

test('user can delete card', function () {
  $card = Card::factory()->create(['column_id' => $this->column->id, 'position' => 0]);

  $response = $this->actingAs($this->user)
    ->deleteJson("/api/cards/{$card->id}");

  $response->assertStatus(204);
  $this->assertDatabaseMissing('cards', ['id' => $card->id]);
});
