<?php

namespace App\Enums;

enum ActivityType: string
{
  case CREATED = 'created';
  case UPDATED = 'updated';
  case MOVED = 'moved';
  case ASSIGNED = 'assigned';
  case UNASSIGNED = 'unassigned';
  case COMMENTED = 'commented';
  case LABEL_ADDED = 'label_added';
  case LABEL_REMOVED = 'label_removed';
  case DUE_DATE_SET = 'due_date_set';
  case DUE_DATE_REMOVED = 'due_date_removed';
}
