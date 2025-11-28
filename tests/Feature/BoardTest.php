<?php

use App\Models\Board;
use App\Models\Team;
use App\Models\User;

beforeEach(function () {
  $this->user = User::factory()->create();
  $this->team = Team::factory()->create(['owner_id' => $this->user->id]);
  $this->team->members()->attach($this->user->id, ['role' => 'owner']);
});

test('user can list boards in their team', function () {
  Board::factory(3)->create(['team_id' => $this->team->id]);

  $response = $this->actingAs($this->user)
    ->getJson("/api/teams/{$this->team->id}/boards");

  $response->assertOk()
    ->assertJsonCount(3, 'data');
});

test('user can create a board', function () {
  $response = $this->actingAs($this->user)
    ->postJson("/api/teams/{$this->team->id}/boards", [
      'name' => 'New Board',
      'description' => 'Board description',
      'color' => '#3b82f6',
    ]);

  $response->assertStatus(201)
    ->assertJsonPath('data.name', 'New Board')
    ->assertJsonCount(4, 'data.columns'); // Default columns created

  $this->assertDatabaseHas('boards', [
    'name' => 'New Board',
    'team_id' => $this->team->id,
  ]);
});

test('user can view board with columns and cards', function () {
  $board = Board::factory()->create(['team_id' => $this->team->id]);
  $column = $board->columns()->create(['name' => 'To Do', 'position' => 0]);
  $column->cards()->create(['title' => 'Test Card', 'position' => 0]);

  $response = $this->actingAs($this->user)
    ->getJson("/api/boards/{$board->id}");

  $response->assertOk()
    ->assertJsonPath('data.name', $board->name)
    ->assertJsonCount(1, 'data.columns')
    ->assertJsonCount(1, 'data.columns.0.cards');
});

test('user cannot access board from team they are not member of', function () {
  $otherTeam = Team::factory()->create();
  $board = Board::factory()->create(['team_id' => $otherTeam->id]);

  $response = $this->actingAs($this->user)
    ->getJson("/api/boards/{$board->id}");

  $response->assertStatus(403);
});

test('user can update board', function () {
  $board = Board::factory()->create(['team_id' => $this->team->id]);

  $response = $this->actingAs($this->user)
    ->putJson("/api/boards/{$board->id}", [
      'name' => 'Updated Board Name',
    ]);

  $response->assertOk()
    ->assertJsonPath('data.name', 'Updated Board Name');
});

test('only admin can delete board', function () {
  $board = Board::factory()->create(['team_id' => $this->team->id]);

  // Member cannot delete
  $member = User::factory()->create();
  $this->team->members()->attach($member->id, ['role' => 'member']);

  $response = $this->actingAs($member)
    ->deleteJson("/api/boards/{$board->id}");

  $response->assertStatus(403);

  // Owner can delete
  $response = $this->actingAs($this->user)
    ->deleteJson("/api/boards/{$board->id}");

  $response->assertStatus(204);
  $this->assertDatabaseMissing('boards', ['id' => $board->id]);
});
