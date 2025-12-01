<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class Board extends Model
{
  use HasFactory;

  protected $fillable = [
    'team_id',
    'name',
    'description',
    'color',
    'is_public',
    'public_token',
    'public_expires_at',
  ];

  protected $casts = [
    'is_public' => 'boolean',
    'public_expires_at' => 'datetime',
  ];

  public function team(): BelongsTo
  {
    return $this->belongsTo(Team::class);
  }

  public function columns(): HasMany
  {
    return $this->hasMany(Column::class)->orderBy('position');
  }

  public function activities(): HasMany
  {
    return $this->hasMany(Activity::class)->orderByDesc('created_at');
  }

  public function labels(): HasMany
  {
    return $this->hasMany(Label::class);
  }

  public function cards(): HasManyThrough
  {
    return $this->hasManyThrough(Card::class, Column::class);
  }

  public function enablePublicSharing(): void
  {
    if (!$this->public_token) {
      $this->public_token = (string) Str::uuid();
    }
    $this->is_public = true;
    $this->save();
  }

  public function disablePublicSharing(): void
  {
    $this->is_public = false;
    $this->save();
  }

  public function regeneratePublicToken(): void
  {
    $this->public_token = (string) Str::uuid();
    $this->save();
  }

  public function isPublicLinkValid(): bool
  {
    if (!$this->is_public || !$this->public_token) {
      return false;
    }

    if ($this->public_expires_at && $this->public_expires_at->isPast()) {
      return false;
    }

    return true;
  }

  public function getPublicUrlAttribute(): ?string
  {
    if (!$this->public_token) {
      return null;
    }

    return config('app.frontend_url', config('app.url')) . '/p/' . $this->public_token;
  }
}
