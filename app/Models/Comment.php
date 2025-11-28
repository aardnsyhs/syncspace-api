<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
  use HasFactory;

  protected $fillable = [
    'card_id',
    'user_id',
    'body',
  ];

  public function card(): BelongsTo
  {
    return $this->belongsTo(Card::class);
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
