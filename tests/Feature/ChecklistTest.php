<?php

// tests/Feature/ChecklistTest.php

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
  $this->column = Column::factory()->create(['board_id' => $this->board->id]);
  $this->card = Card::factory()->create(['column_id' => $this->column->id]);
});

test('user can create checklist on card', function () {
  $response = $this->actingAs($this->user)
    ->postJson("/api/cards/{$this->card->id}/checklists", [
      'title' => 'My Checklist',
    ]);

  $response->assertStatus(201)
    ->assertJsonPath('data.title', 'My Checklist')
    ->assertJsonPath('data.card_id', $this->card->id);
});

test('user can add items to checklist', function () {
  // Create checklist first via API
  $checklistResponse = $this->actingAs($this->user)
    ->postJson("/api/cards/{$this->card->id}/checklists", [
      'title' => 'Test Checklist',
    ]);
  $checklistId = $checklistResponse->json('data.id');

  $response = $this->actingAs($this->user)
    ->postJson("/api/checklists/{$checklistId}/items", [
      'title' => 'Task 1',
    ]);

  $response->assertStatus(201)
    ->assertJsonPath('data.title', 'Task 1');
});

test('user can delete checklist', function () {
  // Create checklist via API
  $checklistResponse = $this->actingAs($this->user)
    ->postJson("/api/cards/{$this->card->id}/checklists", [
      'title' => 'Test Checklist',
    ]);
  $checklistId = $checklistResponse->json('data.id');

  $response = $this->actingAs($this->user)
    ->deleteJson("/api/checklists/{$checklistId}");

  $response->assertStatus(204);
});
