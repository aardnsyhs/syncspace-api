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

  public function ownedTeams(): HasMany
  {
    return $this->hasMany(Team::class, 'owner_id');
  }

  public function teams(): BelongsToMany
  {
    return $this->belongsToMany(Team::class)
      ->withPivot('role')
      ->withTimestamps();
  }

  public function assignedCards(): HasMany
  {
    return $this->hasMany(Card::class, 'assignee_id');
  }

  public function comments(): HasMany
  {
    return $this->hasMany(Comment::class);
  }

  public function activities(): HasMany
  {
    return $this->hasMany(Activity::class);
  }
}
