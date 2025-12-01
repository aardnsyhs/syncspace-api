<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
  protected $fillable = [
    'email',
    'otp',
    'expires_at',
    'verified_at',
  ];

  protected $casts = [
    'expires_at' => 'datetime',
    'verified_at' => 'datetime',
  ];

  public function isExpired(): bool
  {
    return $this->expires_at->isPast();
  }

  public function isVerified(): bool
  {
    return $this->verified_at !== null;
  }

  public static function generateOTP(): string
  {
    return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
  }
}
