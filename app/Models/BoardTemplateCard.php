<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoardTemplateCard extends Model
{
  use HasFactory;

  protected $fillable = [
    'board_template_column_id',
    'title',
    'description',
    'position',
  ];

  protected $casts = [
    'position' => 'integer',
  ];

  public function column(): BelongsTo
  {
    return $this->belongsTo(BoardTemplateColumn::class, 'board_template_column_id');
  }
}
