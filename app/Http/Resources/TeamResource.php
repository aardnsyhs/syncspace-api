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
        'color' => $board->color,
      ])),
      'created_at' => $this->created_at->toISOString(),
    ];
  }
}
