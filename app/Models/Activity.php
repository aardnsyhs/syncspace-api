<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
  use HasFactory;

  public $timestamps = false;

  protected $fillable = [
    'board_id',
    'card_id',
    'user_id',
    'type',
    'data',
    'created_at',
  ];

  protected $casts = [
    'data' => 'array',
    'created_at' => 'datetime',
  ];

  public function board(): BelongsTo
  {
    return $this->belongsTo(Board::class);
  }

  public function card(): BelongsTo
  {
    return $this->belongsTo(Card::class);
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
