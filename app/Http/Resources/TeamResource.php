<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'slug' => $this->slug,
      'owner_id' => $this->owner_id,
      'role' => $this->whenPivotLoaded('team_user', fn() => $this->pivot->role),
      'members_count' => $this->whenCounted('members'),
      'boards_count' => $this->whenCounted('boards'),
      'boards' => $this->whenLoaded('boards', fn() => $this->boards->map(fn($board) => [
        'id' => $board->id,
        'name' => $board->name,
        'description' => $board->description,
        'color' => $board->color,
        'cards_count' => $board->cards_count ?? 0,
        'members_count' => $board->members_count ?? 0,
        'created_at' => $board->created_at?->toISOString(),
      ])),
      'created_at' => $this->created_at->toISOString(),
    ];
  }
}
