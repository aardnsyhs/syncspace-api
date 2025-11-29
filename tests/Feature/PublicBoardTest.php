<?php

// tests/Feature/PublicBoardTest.php

use App\Models\Board;
use App\Models\Card;
use App\Models\Column;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;

beforeEach(function () {
  $this->owner = User::factory()->create();
  $this->team = Team::factory()->create(['owner_id' => $this->owner->id]);
  $this->team->members()->attach($this->owner->id, ['role' => 'owner']);
  $this->board = Board::factory()->create([
    'team_id' => $this->team->id,
    'is_public' => false,
  ]);
});

test('owner can enable public sharing', function () {
  $response = $this->actingAs($this->owner)
    ->postJson("/api/boards/{$this->board->id}/public/enable");

  $response->assertOk()
    ->assertJsonStructure([
      'data' => [
        'is_public',
        'public_token',
        'public_url',
      ],
    ]);

  expect($response->json('data.is_public'))->toBeTrue();
  expect($response->json('data.public_token'))->not->toBeNull();

  $this->assertDatabaseHas('boards', [
    'id' => $this->board->id,
    'is_public' => true,
  ]);
});

test('owner can disable public sharing', function () {
  $this->board->update([
    'is_public' => true,
    'public_token' => Str::random(32),
  ]);

  $response = $this->actingAs($this->owner)
    ->postJson("/api/boards/{$this->board->id}/public/disable");

  $response->assertOk();

  $this->assertDatabaseHas('boards', [
    'id' => $this->board->id,
    'is_public' => false,
  ]);
});

test('owner can regenerate public token', function () {
  $oldToken = Str::random(32);
  $this->board->update([
    'is_public' => true,
    'public_token' => $oldToken,
  ]);

  $response = $this->actingAs($this->owner)
    ->postJson("/api/boards/{$this->board->id}/public/regenerate");

  $response->assertOk();

  $newToken = $response->json('data.public_token');
  expect($newToken)->not->toBe($oldToken);
});

test('member cannot manage public sharing', function () {
  $member = User::factory()->create();
  $this->team->members()->attach($member->id, ['role' => 'member']);

  $response = $this->actingAs($member)
    ->postJson("/api/boards/{$this->board->id}/public/enable");

  $response->assertStatus(403);
});

test('anyone can view public board via token', function () {
  $token = Str::random(32);
  $this->board->update([
    'is_public' => true,
    'public_token' => $token,
  ]);

  // Create some data
  $column = Column::factory()->create(['board_id' => $this->board->id]);
  Card::factory(3)->create(['column_id' => $column->id]);

  // Access without auth
  $response = $this->getJson("/api/public/boards/{$token}");

  $response->assertOk()
    ->assertJsonStructure([
      'data' => [
        'name',
        'columns' => [
          '*' => [
            'id',
            'name',
            'cards',
          ],
        ],
      ],
    ]);

  // Should not expose sensitive data
  $response->assertJsonMissing(['team_id']);
});

test('invalid token returns 404', function () {
  $response = $this->getJson('/api/public/boards/invalid-token');

  $response->assertStatus(404);
});

test('private board token returns 404', function () {
  $token = Str::random(32);
  $this->board->update([
    'is_public' => false,
    'public_token' => $token,
  ]);

  $response = $this->getJson("/api/public/boards/{$token}");

  $response->assertStatus(404);
});
