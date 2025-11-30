<?php

namespace App\Enums;

enum TeamRole: string
{
  case OWNER = 'owner';
  case ADMIN = 'admin';
  case MEMBER = 'member';
  case VIEWER = 'viewer';

  public static function values(): array
  {
    return array_column(self::cases(), 'value');
  }

  public static function assignableByAdmin(): array
  {
    return [self::ADMIN, self::MEMBER, self::VIEWER];
  }

  public function canManageTeam(): bool
  {
    return \in_array($this, [self::OWNER, self::ADMIN], true);
  }

  public function canManageMembers(): bool
  {
    return \in_array($this, [self::OWNER, self::ADMIN], true);
  }

  public function canDeleteTeam(): bool
  {
    return $this === self::OWNER;
  }

  public function canManageBoards(): bool
  {
    return \in_array($this, [self::OWNER, self::ADMIN], true);
  }

  public function canEditContent(): bool
  {
    return \in_array($this, [self::OWNER, self::ADMIN, self::MEMBER], true);
  }

  public function canViewBoards(): bool
  {
    return true; 
  }

  public function isHigherThan(TeamRole $other): bool
  {
    $hierarchy = [
      self::OWNER->value => 4,
      self::ADMIN->value => 3,
      self::MEMBER->value => 2,
      self::VIEWER->value => 1,
    ];

    return $hierarchy[$this->value] > $hierarchy[$other->value];
  }

  public function label(): string
  {
    return match ($this) {
      self::OWNER => 'Owner',
      self::ADMIN => 'Admin',
      self::MEMBER => 'Member',
      self::VIEWER => 'Viewer',
    };
  }
}
