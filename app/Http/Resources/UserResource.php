<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'email' => $this->email,
      'avatar_url' => $this->avatar_url,
      'role' => $this->whenPivotLoaded('team_user', fn() => $this->pivot->role),
      'created_at' => $this->created_at->toISOString(),
    ];
  }
}
