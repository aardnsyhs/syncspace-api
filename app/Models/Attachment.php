<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
  use HasFactory;

  protected $fillable = [
    'card_id',
    'file_name',
    'file_path',
    'file_size',
    'mime_type',
    'is_external',
    'uploaded_by',
  ];

  protected $casts = [
    'file_size' => 'integer',
    'is_external' => 'boolean',
  ];

  protected $appends = ['url'];

  public function card(): BelongsTo
  {
    return $this->belongsTo(Card::class);
  }

  public function uploader(): BelongsTo
  {
    return $this->belongsTo(User::class, 'uploaded_by');
  }

  public function getUrlAttribute(): string
  {
    if ($this->is_external) {
      return $this->file_path;
    }

    return Storage::disk('public')->url($this->file_path);
  }

  public function getFileSizeFormattedAttribute(): string
  {
    $bytes = $this->file_size;

    if ($bytes >= 1073741824) {
      return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
      return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
      return number_format($bytes / 1024, 2) . ' KB';
    }

    return $bytes . ' bytes';
  }
}
