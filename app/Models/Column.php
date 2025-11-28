<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Column extends Model
{
  use HasFactory;

  protected $fillable = [
    'board_id',
    'name',
    'position',
    'wip_limit',
  ];

  protected $casts = [
    'position' => 'integer',
    'wip_limit' => 'integer',
  ];

  public function board(): BelongsTo
  {
    return $this->belongsTo(Board::class);
  }

  public function cards(): HasMany
  {
    return $this->hasMany(Card::class)->orderBy('position');
  }

  /**
   * Check if WIP limit is exceeded
   */
  public function isWipExceeded(): bool
  {
    if ($this->wip_limit === null) {
      return false;
    }

    return $this->cards()->count() > $this->wip_limit;
  }

  /**
   * Check if adding one more card would exceed WIP limit
   */
  public function wouldExceedWip(): bool
  {
    if ($this->wip_limit === null) {
      return false;
    }

    return $this->cards()->count() >= $this->wip_limit;
  }

  /**
   * Get WIP status info
   */
  public function getWipStatusAttribute(): array
  {
    $count = $this->cards()->count();
    $limit = $this->wip_limit;

    return [
      'count' => $count,
      'limit' => $limit,
      'exceeded' => $limit !== null && $count > $limit,
      'at_limit' => $limit !== null && $count === $limit,
    ];
  }
}
