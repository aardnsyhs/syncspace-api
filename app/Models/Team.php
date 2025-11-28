<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'slug',
    'owner_id',
  ];

  public function owner(): BelongsTo
  {
    return $this->belongsTo(User::class, 'owner_id');
  }

  public function members(): BelongsToMany
  {
    return $this->belongsToMany(User::class)
      ->withPivot('role')
      ->withTimestamps();
  }

  public function boards(): HasMany
  {
    return $this->hasMany(Board::class);
  }

  public function labels(): HasMany
  {
    return $this->hasMany(Label::class);
  }

  public function hasMember(User $user): bool
  {
    return $this->members()->where('user_id', $user->id)->exists();
  }

  public function getMemberRole(User $user): ?string
  {
    $member = $this->members()->where('user_id', $user->id)->first();
    return $member?->pivot->role;
  }
}
