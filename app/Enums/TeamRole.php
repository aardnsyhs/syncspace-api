<?php

namespace App\Enums;

enum TeamRole: string
{
  case OWNER = 'owner';
  case ADMIN = 'admin';
  case MEMBER = 'member';

  public function canManageTeam(): bool
  {
    return in_array($this, [self::OWNER, self::ADMIN]);
  }

  public function canManageMembers(): bool
  {
    return in_array($this, [self::OWNER, self::ADMIN]);
  }

  public function canDeleteTeam(): bool
  {
    return $this === self::OWNER;
  }
}
