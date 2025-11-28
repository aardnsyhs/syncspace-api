<?php

namespace App\Enums;

enum TeamRole: string
{
  case OWNER = 'owner';
  case ADMIN = 'admin';
  case MEMBER = 'member';
  case VIEWER = 'viewer';

  /**
   * Get all available roles
   */
  public static function values(): array
  {
    return array_column(self::cases(), 'value');
  }

  /**
   * Get roles that can be assigned by an admin
   */
  public static function assignableByAdmin(): array
  {
    return [self::ADMIN, self::MEMBER, self::VIEWER];
  }

  /**
   * Check if this role can manage team settings
   */
  public function canManageTeam(): bool
  {
    return \in_array($this, [self::OWNER, self::ADMIN], true);
  }

  /**
   * Check if this role can manage team members
   */
  public function canManageMembers(): bool
  {
    return \in_array($this, [self::OWNER, self::ADMIN], true);
  }

  /**
   * Check if this role can delete the team
   */
  public function canDeleteTeam(): bool
  {
    return $this === self::OWNER;
  }

  /**
   * Check if this role can create/delete boards
   */
  public function canManageBoards(): bool
  {
    return \in_array($this, [self::OWNER, self::ADMIN], true);
  }

  /**
   * Check if this role can edit board content (columns, cards)
   */
  public function canEditContent(): bool
  {
    return \in_array($this, [self::OWNER, self::ADMIN, self::MEMBER], true);
  }

  /**
   * Check if this role can view boards
   */
  public function canViewBoards(): bool
  {
    return true; // All roles can view
  }

  /**
   * Check if this role is higher than another
   */
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

  /**
   * Get human-readable label
   */
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
