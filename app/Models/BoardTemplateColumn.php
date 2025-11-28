<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoardTemplateColumn extends Model
{
  use HasFactory;

  protected $fillable = [
    'board_template_id',
    'name',
    'position',
    'wip_limit',
  ];

  protected $casts = [
    'position' => 'integer',
    'wip_limit' => 'integer',
  ];

  public function template(): BelongsTo
  {
    return $this->belongsTo(BoardTemplate::class, 'board_template_id');
  }

  public function cards(): HasMany
  {
    return $this->hasMany(BoardTemplateCard::class)->orderBy('position');
  }
}
