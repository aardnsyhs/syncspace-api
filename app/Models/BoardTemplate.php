<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BoardTemplate extends Model
{
  use HasFactory;

  protected $fillable = [
    'team_id',
    'name',
    'description',
    'slug',
    'visibility',
    'created_by',
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($template) {
      if (empty($template->slug)) {
        $template->slug = Str::slug($template->name) . '-' . Str::random(6);
      }
    });
  }

  public function team(): BelongsTo
  {
    return $this->belongsTo(Team::class);
  }

  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function columns(): HasMany
  {
    return $this->hasMany(BoardTemplateColumn::class)->orderBy('position');
  }

  public function isGlobal(): bool
  {
    return $this->visibility === 'global';
  }

  /**
   * Check if user can access this template
   */
  public function isAccessibleBy(User $user): bool
  {
    if ($this->isGlobal()) {
      return true;
    }

    if ($this->team_id === null) {
      return false;
    }

    return $this->team->members()->where('user_id', $user->id)->exists();
  }
}
