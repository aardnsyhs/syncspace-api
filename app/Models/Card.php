<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
  use HasFactory;

  protected $fillable = [
    'column_id',
    'title',
    'description',
    'position',
    'assignee_id',
    'due_date',
  ];

  protected $casts = [
    'position' => 'integer',
    'due_date' => 'date',
  ];

  public function column(): BelongsTo
  {
    return $this->belongsTo(Column::class);
  }

  public function assignee(): BelongsTo
  {
    return $this->belongsTo(User::class, 'assignee_id');
  }

  public function comments(): HasMany
  {
    return $this->hasMany(Comment::class)->orderBy('created_at');
  }

  public function labels(): BelongsToMany
  {
    return $this->belongsToMany(Label::class);
  }

  public function checklists(): HasMany
  {
    return $this->hasMany(Checklist::class)->orderBy('position');
  }

  public function attachments(): HasMany
  {
    return $this->hasMany(Attachment::class)->orderByDesc('created_at');
  }

  public function activities(): HasMany
  {
    return $this->hasMany(Activity::class)->orderByDesc('created_at');
  }

  public function getChecklistProgressAttribute(): array
  {
    $total = 0;
    $completed = 0;

    foreach ($this->checklists as $checklist) {
      $total += $checklist->items()->count();
      $completed += $checklist->items()->where('is_completed', true)->count();
    }

    return [
      'total' => $total,
      'completed' => $completed,
      'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
    ];
  }
}
