<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
  use HasFactory;

  protected $fillable = [
    'checklist_id',
    'title',
    'is_completed',
    'position',
    'completed_at',
  ];

  protected $casts = [
    'is_completed' => 'boolean',
    'position' => 'integer',
    'completed_at' => 'datetime',
  ];

  public function checklist(): BelongsTo
  {
    return $this->belongsTo(Checklist::class);
  }
}
