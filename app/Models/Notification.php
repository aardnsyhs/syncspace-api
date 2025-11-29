<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'type',
    'title',
    'message',
    'data',
    'read_at',
  ];

  protected $casts = [
    'data' => 'array',
    'read_at' => 'datetime',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function isRead(): bool
  {
    return $this->read_at !== null;
  }

  public function markAsRead(): void
  {
    $this->update(['read_at' => now()]);
  }

  public function scopeUnread($query)
  {
    return $query->whereNull('read_at');
  }

  public function scopeForUser($query, int $userId)
  {
    return $query->where('user_id', $userId);
  }
}
