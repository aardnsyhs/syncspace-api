<?php

// tests/Feature/TeamRbacTest.php

use App\Models\Board;
use App\Models\Team;
use App\Models\User;

beforeEach(function () {
  $this->owner = User::factory()->create();
  $this->admin = User::factory()->create();
  $this->member = User::factory()->create();
  $this->outsider = User::factory()->create();

  $this->team = Team::factory()->create(['owner_id' => $this->owner->id]);
  $this->team->members()->attach($this->owner->id, ['role' => 'owner']);
  $this->team->members()->attach($this->admin->id, ['role' => 'admin']);
  $this->team->members()->attach($this->member->id, ['role' => 'member']);

  $this->board = Board::factory()->create(['team_id' => $this->team->id]);
});

test('owner can invite members to team', function () {
  $newUser = User::factory()->create();

  $response = $this->actingAs($this->owner)
    ->postJson("/api/teams/{$this->team->id}/members", [
      'email' => $newUser->email,
      'role' => 'member',
    ]);

  $response->assertStatus(201);
  $this->assertDatabaseHas('team_user', [
    'team_id' => $this->team->id,
    'user_id' => $newUser->id,
    'role' => 'member',
  ]);
});

test('admin can invite members to team', function () {
  $newUser = User::factory()->create();

  $response = $this->actingAs($this->admin)
    ->postJson("/api/teams/{$this->team->id}/members", [
      'email' => $newUser->email,
      'role' => 'member',
    ]);

  $response->assertStatus(201);
});

test('member cannot invite others to team', function () {
  $newUser = User::factory()->create();

  $response = $this->actingAs($this->member)
    ->postJson("/api/teams/{$this->team->id}/members", [
      'email' => $newUser->email,
      'role' => 'member',
    ]);

  $response->assertStatus(403);
});

test('owner can change member roles', function () {
  // Owner can change role
  $response = $this->actingAs($this->owner)
    ->patchJson("/api/teams/{$this->team->id}/members/{$this->member->id}", [
      'role' => 'admin',
    ]);

  $response->assertOk();
  $this->assertDatabaseHas('team_user', [
    'team_id' => $this->team->id,
    'user_id' => $this->member->id,
    'role' => 'admin',
  ]);
});

test('owner can remove members', function () {
  // Owner can remove member
  $response = $this->actingAs($this->owner)
    ->deleteJson("/api/teams/{$this->team->id}/members/{$this->member->id}");

  $response->assertStatus(204);
});

test('outsider cannot access team resources', function () {
  $response = $this->actingAs($this->outsider)
    ->getJson("/api/teams/{$this->team->id}/boards");

  $response->assertStatus(403);
});

test('member can view boards but not delete', function () {
  // Can view
  $response = $this->actingAs($this->member)
    ->getJson("/api/boards/{$this->board->id}");

  $response->assertOk();

  // Cannot delete
  $response = $this->actingAs($this->member)
    ->deleteJson("/api/boards/{$this->board->id}");

  $response->assertStatus(403);
});

test('admin can create and delete boards', function () {
  // Can create
  $response = $this->actingAs($this->admin)
    ->postJson("/api/teams/{$this->team->id}/boards", [
      'name' => 'Admin Board',
    ]);

  $response->assertStatus(201);
  $boardId = $response->json('data.id');

  // Can delete
  $response = $this->actingAs($this->admin)
    ->deleteJson("/api/boards/{$boardId}");

  $response->assertStatus(204);
});
