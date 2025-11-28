<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory, Notifiable, HasApiTokens;

  protected $fillable = [
    'name',
    'email',
    'password',
    'avatar_url',
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  // Teams yang dimiliki user (sebagai owner)
  public function ownedTeams(): HasMany
  {
    return $this->hasMany(Team::class, 'owner_id');
  }

  // Teams yang user ikuti (termasuk owned)
  public function teams(): BelongsToMany
  {
    return $this->belongsToMany(Team::class)
      ->withPivot('role')
      ->withTimestamps();
  }

  // Cards yang di-assign ke user
  public function assignedCards(): HasMany
  {
    return $this->hasMany(Card::class, 'assignee_id');
  }

  // Comments yang dibuat user
  public function comments(): HasMany
  {
    return $this->hasMany(Comment::class);
  }

  // Activity log user
  public function activities(): HasMany
  {
    return $this->hasMany(Activity::class);
  }
}
