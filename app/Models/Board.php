<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
  use HasFactory;

  protected $fillable = [
    'team_id',
    'name',
    'description',
    'color',
  ];

  public function team(): BelongsTo
  {
    return $this->belongsTo(Team::class);
  }

  public function columns(): HasMany
  {
    return $this->hasMany(Column::class)->orderBy('position');
  }
}
