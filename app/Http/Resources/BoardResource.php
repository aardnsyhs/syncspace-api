<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'team_id' => $this->team_id,
      'name' => $this->name,
      'description' => $this->description,
      'color' => $this->color,
      'columns' => ColumnResource::collection($this->whenLoaded('columns')),
      'created_at' => $this->created_at->toISOString(),
    ];
  }
}
