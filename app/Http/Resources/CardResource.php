<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'column_id' => $this->column_id,
      'title' => $this->title,
      'description' => $this->description,
      'position' => $this->position,
      'assignee_id' => $this->assignee_id,
      'assignee' => new UserResource($this->whenLoaded('assignee')),
      'due_date' => $this->due_date?->toDateString(),
      'labels' => LabelResource::collection($this->whenLoaded('labels')),
      'comments_count' => $this->whenCounted('comments'),
      'created_at' => $this->created_at->toISOString(),
    ];
  }
}
