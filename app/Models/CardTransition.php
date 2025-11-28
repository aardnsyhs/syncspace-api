<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardTransition extends Model
{
  public $timestamps = false;

  protected $fillable = [
    'card_id',
    'from_column_id',
    'to_column_id',
    'user_id',
    'transitioned_at',
  ];

  protected $casts = [
    'transitioned_at' => 'datetime',
  ];

  public function card(): BelongsTo
  {
    return $this->belongsTo(Card::class);
  }

  public function fromColumn(): BelongsTo
  {
    return $this->belongsTo(Column::class, 'from_column_id');
  }

  public function toColumn(): BelongsTo
  {
    return $this->belongsTo(Column::class, 'to_column_id');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
