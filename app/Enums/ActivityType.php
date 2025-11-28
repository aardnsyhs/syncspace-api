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
  case CHECKLIST_ADDED = 'checklist_added';
  case CHECKLIST_REMOVED = 'checklist_removed';
  case CHECKLIST_ITEM_COMPLETED = 'checklist_item_completed';
  case CHECKLIST_ITEM_UNCOMPLETED = 'checklist_item_uncompleted';
  case ATTACHMENT_ADDED = 'attachment_added';
  case ATTACHMENT_REMOVED = 'attachment_removed';
  case BOARD_CREATED_FROM_TEMPLATE = 'board_created_from_template';
  case BOARD_SAVED_AS_TEMPLATE = 'board_saved_as_template';
  case PUBLIC_SHARING_ENABLED = 'public_sharing_enabled';
  case PUBLIC_SHARING_DISABLED = 'public_sharing_disabled';
}
