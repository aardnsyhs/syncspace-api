<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Checklist extends Model
{
  use HasFactory;

  protected $fillable = [
    'card_id',
    'title',
    'position',
  ];

  protected $casts = [
    'position' => 'integer',
  ];

  public function card(): BelongsTo
  {
    return $this->belongsTo(Card::class);
  }

  public function items(): HasMany
  {
    return $this->hasMany(ChecklistItem::class)->orderBy('position');
  }

  public function getProgressAttribute(): array
  {
    $total = $this->items()->count();
    $completed = $this->items()->where('is_completed', true)->count();

    return [
      'total' => $total,
      'completed' => $completed,
      'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
    ];
  }
}
