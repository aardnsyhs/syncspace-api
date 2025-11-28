<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Label extends Model
{
  use HasFactory;

  protected $fillable = [
    'board_id',
    'name',
    'color',
  ];

  public function board(): BelongsTo
  {
    return $this->belongsTo(Board::class);
  }

  public function cards(): BelongsToMany
  {
    return $this->belongsToMany(Card::class);
  }
}
