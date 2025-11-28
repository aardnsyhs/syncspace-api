<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'type' => $this->type,
      'data' => $this->data,
      'user' => [
        'id' => $this->user->id,
        'name' => $this->user->name,
        'avatar_url' => $this->user->avatar_url,
      ],
      'card_id' => $this->card_id,
      'created_at' => $this->created_at->toISOString(),
    ];
  }
}
