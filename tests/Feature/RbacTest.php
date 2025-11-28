<?php

use App\Models\Board;
use App\Models\Card;
use App\Models\Column;
use App\Models\Team;
use App\Models\User;

beforeEach(function () {
  // Create team with owner
  $this->owner = User::factory()->create();
  $this->admin = User::factory()->create();
  $this->member = User::factory()->create();
  $this->viewer = User::factory()->create();
  $this->outsider = User::factory()->create();

  $this->team = Team::factory()->create(['owner_id' => $this->owner->id]);
  $this->team->members()->attach($this->owner->id, ['role' => 'owner']);
  $this->team->members()->attach($this->admin->id, ['role' => 'admin']);
  $this->team->members()->attach($this->member->id, ['role' => 'member']);
  $this->team->members()->attach($this->viewer->id, ['role' => 'viewer']);

  $this->board = Board::factory()->create(['team_id' => $this->team->id]);
  $this->column = Column::factory()->create(['board_id' => $this->board->id, 'position' => 0]);
  $this->card = Card::factory()->create(['column_id' => $this->column->id, 'position' => 0]);
});

// Board deletion tests
test('owner can delete board', function () {
  $response = $this->actingAs($this->owner)
    ->deleteJson("/api/boards/{$this->board->id}");

  $response->assertStatus(204);
});

test('admin can delete board', function () {
  $response = $this->actingAs($this->admin)
    ->deleteJson("/api/boards/{$this->board->id}");

  $response->assertStatus(204);
});

test('member cannot delete board', function () {
  $response = $this->actingAs($this->member)
    ->deleteJson("/api/boards/{$this->board->id}");

  $response->assertStatus(403);
});

test('viewer cannot delete board', function () {
  $response = $this->actingAs($this->viewer)
    ->deleteJson("/api/boards/{$this->board->id}");

  $response->assertStatus(403);
});

// Card modification tests
test('member can create card', function () {
  $response = $this->actingAs($this->member)
    ->postJson("/api/columns/{$this->column->id}/cards", [
      'title' => 'New Card',
    ]);

  $response->assertStatus(201);
});

test('viewer cannot create card', function () {
  $response = $this->actingAs($this->viewer)
    ->postJson("/api/columns/{$this->column->id}/cards", [
      'title' => 'New Card',
    ]);

  $response->assertStatus(403);
});

test('viewer cannot update card', function () {
  $response = $this->actingAs($this->viewer)
    ->putJson("/api/cards/{$this->card->id}", [
      'title' => 'Updated Title',
    ]);

  $response->assertStatus(403);
});

test('viewer cannot move card', function () {
  $response = $this->actingAs($this->viewer)
    ->putJson("/api/cards/{$this->card->id}/move", [
      'column_id' => $this->column->id,
      'position' => 0,
    ]);

  $response->assertStatus(403);
});

test('viewer can view board', function () {
  $response = $this->actingAs($this->viewer)
    ->getJson("/api/boards/{$this->board->id}");

  $response->assertStatus(200);
});

// Outsider tests
test('outsider cannot access board', function () {
  $response = $this->actingAs($this->outsider)
    ->getJson("/api/boards/{$this->board->id}");

  $response->assertStatus(403);
});

// Member management tests
test('admin can invite member', function () {
  $newUser = User::factory()->create();

  $response = $this->actingAs($this->admin)
    ->postJson("/api/teams/{$this->team->id}/members", [
      'email' => $newUser->email,
      'role' => 'member',
    ]);

  $response->assertStatus(201);
});

test('member cannot invite member', function () {
  $newUser = User::factory()->create();

  $response = $this->actingAs($this->member)
    ->postJson("/api/teams/{$this->team->id}/members", [
      'email' => $newUser->email,
      'role' => 'member',
    ]);

  $response->assertStatus(403);
});

test('admin cannot add another admin', function () {
  $newUser = User::factory()->create();

  $response = $this->actingAs($this->admin)
    ->postJson("/api/teams/{$this->team->id}/members", [
      'email' => $newUser->email,
      'role' => 'admin',
    ]);

  $response->assertStatus(403);
});

test('owner can add admin', function () {
  $newUser = User::factory()->create();

  $response = $this->actingAs($this->owner)
    ->postJson("/api/teams/{$this->team->id}/members", [
      'email' => $newUser->email,
      'role' => 'admin',
    ]);

  $response->assertStatus(201);
});

test('cannot remove team owner', function () {
  $response = $this->actingAs($this->admin)
    ->deleteJson("/api/teams/{$this->team->id}/members/{$this->owner->id}");

  $response->assertStatus(422);
});
