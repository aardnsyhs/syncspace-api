# SyncSpace API Documentation

Base URL: `http://your-domain.com/api`

## Table of Contents

-   [Authentication](#authentication)
-   [User & Profile](#user--profile)
-   [Teams](#teams)
-   [Team Members](#team-members)
-   [Boards](#boards)
-   [Columns](#columns)
-   [Cards](#cards)
-   [Comments](#comments)
-   [Labels](#labels)
-   [Checklists](#checklists)
-   [Attachments](#attachments)
-   [Notifications](#notifications)
-   [Dashboard](#dashboard)
-   [Board Analytics](#board-analytics)
-   [Board Templates](#board-templates)
-   [Public Boards](#public-boards)

---

## Authentication

All authenticated endpoints require Bearer token in header:

```
Authorization: Bearer {token}
```

### Register

```http
POST /register
```

**Request Body:**

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201):**

```json
{
    "message": "Registration successful. Please check your email for verification code.",
    "data": {
        "email": "john@example.com",
        "requires_verification": true
    }
}
```

### Verify OTP

```http
POST /verify-otp
```

**Request Body:**

```json
{
    "email": "john@example.com",
    "otp": "123456"
}
```

**Response (200):**

```json
{
  "message": "Email verified successfully.",
  "data": { ... },
  "token": "1|abc123..."
}
```

### Resend OTP

```http
POST /resend-otp
```

**Request Body:**

```json
{
    "email": "john@example.com"
}
```

### Login

```http
POST /login
```

**Request Body:**

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200):**

```json
{
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "avatar_url": null,
        "email_notifications": true,
        "desktop_notifications": true,
        "created_at": "2024-01-01T00:00:00.000Z"
    },
    "token": "1|abc123..."
}
```

### Logout

```http
POST /logout
```

🔒 Requires Authentication

**Response (200):**

```json
{
    "message": "Logged out successfully."
}
```

### Forgot Password

```http
POST /forgot-password
```

**Request Body:**

```json
{
    "email": "john@example.com"
}
```

**Response (200):**

```json
{
    "message": "Password reset link has been sent to your email address."
}
```

### Reset Password

```http
POST /reset-password
```

**Request Body:**

```json
{
    "email": "john@example.com",
    "token": "reset-token-from-email",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

---

## User & Profile

### Get Current User

```http
GET /user
```

🔒 Requires Authentication

**Response (200):**

```json
{
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "avatar_url": "https://...",
        "email_notifications": true,
        "desktop_notifications": true,
        "created_at": "2024-01-01T00:00:00.000Z"
    }
}
```

### Update Profile

```http
PUT /user/profile
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "name": "John Updated",
    "avatar_url": "https://example.com/avatar.jpg"
}
```

### Upload Avatar

```http
POST /user/avatar
```

🔒 Requires Authentication

**Request Body (multipart/form-data):**

-   `avatar`: Image file (jpeg, png, jpg, gif, webp, max 2MB)

**Response (200):**

```json
{
  "message": "Avatar uploaded successfully.",
  "data": { ... }
}
```

### Update Password

```http
PUT /user/password
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "current_password": "oldpassword",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

### Update Notification Preferences

```http
PUT /user/notification-preferences
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "email_notifications": true,
    "desktop_notifications": false
}
```

---

## Teams

### List Teams

```http
GET /teams
```

🔒 Requires Authentication

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "name": "My Team",
            "slug": "my-team-abc12",
            "owner_id": 1,
            "role": "owner",
            "members_count": 5,
            "boards_count": 3,
            "boards": [
                {
                    "id": 1,
                    "name": "Project Board",
                    "description": "Main project board",
                    "color": "#3b82f6",
                    "cards_count": 15,
                    "members_count": 5,
                    "created_at": "2024-01-01T00:00:00.000Z"
                }
            ],
            "created_at": "2024-01-01T00:00:00.000Z"
        }
    ]
}
```

### Create Team

```http
POST /teams
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "name": "New Team"
}
```

**Response (201):**

```json
{
    "data": {
        "id": 2,
        "name": "New Team",
        "slug": "new-team-xyz45",
        "owner_id": 1,
        "members_count": 1,
        "boards_count": 0,
        "created_at": "2024-01-01T00:00:00.000Z"
    }
}
```

### Get Team

```http
GET /teams/{team}
```

🔒 Requires Authentication

### Update Team

```http
PUT /teams/{team}
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "name": "Updated Team Name"
}
```

### Delete Team

```http
DELETE /teams/{team}
```

🔒 Requires Authentication

---

## Team Members

### List Members

```http
GET /teams/{team}/members
```

🔒 Requires Authentication

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "avatar_url": null,
            "role": "owner"
        },
        {
            "id": 2,
            "name": "Jane Doe",
            "email": "jane@example.com",
            "avatar_url": null,
            "role": "member"
        }
    ],
    "meta": {
        "current_user_role": "owner"
    }
}
```

### Add Member

```http
POST /teams/{team}/members
```

🔒 Requires Authentication (Admin/Owner)

**Request Body:**

```json
{
    "email": "newmember@example.com",
    "role": "member"
}
```

**Roles:** `owner`, `admin`, `member`, `viewer`

**Response (201):**

```json
{
  "message": "Member added successfully. An invitation email has been sent.",
  "data": { ... }
}
```

### Update Member Role

```http
PATCH /teams/{team}/members/{user}
```

🔒 Requires Authentication (Admin/Owner)

**Request Body:**

```json
{
    "role": "admin"
}
```

### Remove Member

```http
DELETE /teams/{team}/members/{user}
```

🔒 Requires Authentication (Admin/Owner)

---

## Boards

### List Boards

```http
GET /teams/{team}/boards
```

🔒 Requires Authentication

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "team_id": 1,
            "name": "Project Board",
            "description": "Main project board",
            "color": "#3b82f6",
            "created_at": "2024-01-01T00:00:00.000Z"
        }
    ]
}
```

### Create Board

```http
POST /teams/{team}/boards
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "name": "New Board",
    "description": "Board description",
    "color": "#10b981"
}
```

**Response (201):**

```json
{
    "data": {
        "id": 2,
        "team_id": 1,
        "name": "New Board",
        "description": "Board description",
        "color": "#10b981",
        "columns": [
            { "id": 1, "name": "To Do", "position": 0 },
            { "id": 2, "name": "In Progress", "position": 1 },
            { "id": 3, "name": "Review", "position": 2 },
            { "id": 4, "name": "Done", "position": 3 }
        ],
        "created_at": "2024-01-01T00:00:00.000Z"
    }
}
```

### Get Board

```http
GET /boards/{board}
```

🔒 Requires Authentication

**Response (200):**

```json
{
    "data": {
        "id": 1,
        "team_id": 1,
        "name": "Project Board",
        "description": "Main project board",
        "color": "#3b82f6",
        "columns": [
            {
                "id": 1,
                "name": "To Do",
                "position": 0,
                "wip_limit": null,
                "cards": [
                    {
                        "id": 1,
                        "title": "Task 1",
                        "description": "Task description",
                        "position": 0,
                        "due_date": "2024-02-01",
                        "assignee": {
                            "id": 1,
                            "name": "John Doe",
                            "avatar_url": null
                        },
                        "labels": [
                            { "id": 1, "name": "Bug", "color": "#ef4444" }
                        ]
                    }
                ]
            }
        ],
        "created_at": "2024-01-01T00:00:00.000Z"
    }
}
```

### Update Board

```http
PUT /boards/{board}
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "name": "Updated Board Name",
    "description": "Updated description",
    "color": "#8b5cf6"
}
```

### Delete Board

```http
DELETE /boards/{board}
```

🔒 Requires Authentication

### Get Board Activities

```http
GET /boards/{board}/activities
```

🔒 Requires Authentication

**Query Parameters:**

-   `limit` (optional): Number of activities (default: 20)

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "description": "John Doe created card \"Task 1\"",
            "user": {
                "id": 1,
                "name": "John Doe",
                "avatar_url": null
            },
            "created_at": "2024-01-01T00:00:00.000Z"
        }
    ]
}
```

---

## Columns

### Create Column

```http
POST /boards/{board}/columns
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "name": "New Column"
}
```

**Response (201):**

```json
{
    "data": {
        "id": 5,
        "name": "New Column",
        "position": 4,
        "wip_limit": null,
        "cards": []
    }
}
```

### Update Column

```http
PUT /columns/{column}
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "name": "Updated Column Name"
}
```

### Delete Column

```http
DELETE /columns/{column}
```

🔒 Requires Authentication

### Move Column

```http
PUT /columns/{column}/move
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "position": 2
}
```

### Update WIP Limit

```http
PATCH /columns/{column}/wip-limit
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "wip_limit": 5
}
```

Set `wip_limit` to `null` to remove the limit.

---

## Cards

### List Cards in Board

```http
GET /boards/{board}/cards
```

🔒 Requires Authentication

### Create Card

```http
POST /columns/{column}/cards
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "title": "New Task",
    "description": "Task description",
    "due_date": "2024-02-01",
    "assignee_id": 1
}
```

**Response (201):**

```json
{
    "data": {
        "id": 1,
        "title": "New Task",
        "description": "Task description",
        "position": 0,
        "due_date": "2024-02-01",
        "assignee": {
            "id": 1,
            "name": "John Doe",
            "avatar_url": null
        },
        "labels": [],
        "created_at": "2024-01-01T00:00:00.000Z"
    }
}
```

### Get Card

```http
GET /cards/{card}
```

🔒 Requires Authentication

**Response (200):**

```json
{
  "data": {
    "id": 1,
    "title": "Task 1",
    "description": "Task description",
    "position": 0,
    "due_date": "2024-02-01",
    "assignee": { ... },
    "labels": [ ... ],
    "comments": [ ... ],
    "created_at": "2024-01-01T00:00:00.000Z"
  }
}
```

### Update Card

```http
PUT /cards/{card}
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "title": "Updated Title",
    "description": "Updated description",
    "due_date": "2024-03-01",
    "assignee_id": 2
}
```

### Delete Card

```http
DELETE /cards/{card}
```

🔒 Requires Authentication

### Move Card

```http
PUT /cards/{card}/move
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "column_id": 2,
    "position": 0
}
```

**Response (200):**

```json
{
  "data": { ... },
  "wip_warning": {
    "exceeded": true,
    "column_name": "In Progress",
    "limit": 3,
    "count": 4
  }
}
```

---

## Comments

### List Comments

```http
GET /cards/{card}/comments
```

🔒 Requires Authentication

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "body": "This is a comment",
            "user": {
                "id": 1,
                "name": "John Doe",
                "avatar_url": null
            },
            "created_at": "2024-01-01T00:00:00.000Z"
        }
    ]
}
```

### Create Comment

```http
POST /cards/{card}/comments
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "body": "This is a new comment. @Jane Doe check this out!"
}
```

Supports @mentions - mentioned users will receive notifications.

### Delete Comment

```http
DELETE /comments/{comment}
```

🔒 Requires Authentication

---

## Labels

### List Board Labels

```http
GET /boards/{board}/labels
```

🔒 Requires Authentication

**Response (200):**

```json
{
    "data": [
        { "id": 1, "name": "Bug", "color": "#ef4444" },
        { "id": 2, "name": "Feature", "color": "#10b981" }
    ]
}
```

### Create Label

```http
POST /boards/{board}/labels
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "name": "Priority",
    "color": "#f59e0b"
}
```

### Update Label

```http
PATCH /boards/{board}/labels/{label}
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "name": "High Priority",
    "color": "#dc2626"
}
```

### Delete Label

```http
DELETE /boards/{board}/labels/{label}
```

🔒 Requires Authentication

### Add Label to Card

```http
POST /cards/{card}/labels
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "label_id": 1
}
```

### Remove Label from Card

```http
DELETE /cards/{card}/labels/{label}
```

🔒 Requires Authentication

---

## Checklists

### List Checklists

```http
GET /cards/{card}/checklists
```

🔒 Requires Authentication

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "title": "Todo List",
            "position": 0,
            "progress": {
                "total": 3,
                "completed": 1,
                "percentage": 33
            },
            "items": [
                {
                    "id": 1,
                    "title": "Item 1",
                    "is_completed": true,
                    "position": 0
                },
                {
                    "id": 2,
                    "title": "Item 2",
                    "is_completed": false,
                    "position": 1
                }
            ]
        }
    ]
}
```

### Create Checklist

```http
POST /cards/{card}/checklists
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "title": "New Checklist"
}
```

### Update Checklist

```http
PATCH /checklists/{checklist}
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "title": "Updated Title",
    "position": 1
}
```

### Delete Checklist

```http
DELETE /checklists/{checklist}
```

🔒 Requires Authentication

### Add Checklist Item

```http
POST /checklists/{checklist}/items
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "title": "New Item"
}
```

### Update Checklist Item

```http
PATCH /checklist-items/{checklistItem}
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "title": "Updated Item",
    "is_completed": true
}
```

### Delete Checklist Item

```http
DELETE /checklist-items/{checklistItem}
```

🔒 Requires Authentication

---

## Attachments

### List Attachments

```http
GET /cards/{card}/attachments
```

🔒 Requires Authentication

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "file_name": "document.pdf",
            "file_path": "attachments/1/document.pdf",
            "file_size": 102400,
            "mime_type": "application/pdf",
            "is_external": false,
            "uploader": {
                "id": 1,
                "name": "John Doe",
                "avatar_url": null
            },
            "created_at": "2024-01-01T00:00:00.000Z"
        }
    ]
}
```

### Upload Attachment (File)

```http
POST /cards/{card}/attachments
```

🔒 Requires Authentication

**Request Body (multipart/form-data):**

-   `file`: File (max 10MB)

### Add External Link

```http
POST /cards/{card}/attachments
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "url": "https://example.com/document.pdf",
    "file_name": "External Document"
}
```

### Delete Attachment

```http
DELETE /attachments/{attachment}
```

🔒 Requires Authentication

---

## Notifications

### List Notifications

```http
GET /notifications
```

🔒 Requires Authentication

**Query Parameters:**

-   `limit` (optional): Number of notifications (default: 20)

**Response (200):**

```json
{
    "data": [
        {
            "id": "1",
            "type": "card_assigned",
            "title": "You were assigned to a card",
            "message": "John Doe assigned you to \"Task 1\"",
            "data": {
                "card_id": 1,
                "board_id": 1
            },
            "read": false,
            "created_at": "2024-01-01T00:00:00.000Z"
        }
    ],
    "meta": {
        "unread_count": 5
    }
}
```

### Mark as Read

```http
POST /notifications/{notification}/read
```

🔒 Requires Authentication

### Mark All as Read

```http
POST /notifications/read-all
```

🔒 Requires Authentication

### Delete Notification

```http
DELETE /notifications/{notification}
```

🔒 Requires Authentication

### Clear All Notifications

```http
DELETE /notifications
```

🔒 Requires Authentication

---

## Dashboard

### Get Stats

```http
GET /dashboard/stats
```

🔒 Requires Authentication

**Response (200):**

```json
{
    "data": {
        "total_boards": 5,
        "total_cards": 42,
        "cards_due_soon": 3,
        "completed_cards": 15
    }
}
```

### Get Activities

```http
GET /dashboard/activities
```

🔒 Requires Authentication

**Query Parameters:**

-   `limit` (optional): Number of activities (default: 10)

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "description": "John Doe created card \"Task 1\"",
            "created_at": "2024-01-01T00:00:00.000Z",
            "user": {
                "name": "John Doe",
                "avatar_url": null
            },
            "board": {
                "id": 1,
                "name": "Project Board"
            }
        }
    ]
}
```

### Get My Cards

```http
GET /dashboard/my-cards
```

🔒 Requires Authentication

**Query Parameters:**

-   `limit` (optional): Number of cards (default: 10)

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "title": "Task 1",
            "due_date": "2024-02-01",
            "board": {
                "id": 1,
                "name": "Project Board"
            },
            "column": {
                "name": "In Progress"
            }
        }
    ]
}
```

---

## Board Analytics

### Get Summary

```http
GET /boards/{board}/analytics/summary
```

🔒 Requires Authentication

**Response (200):**

```json
{
    "data": {
        "total_cards": 42,
        "completed_cards": 15,
        "in_progress_cards": 20,
        "overdue_cards": 2,
        "avg_completion_time_hours": 48.5
    }
}
```

### Get Throughput

```http
GET /boards/{board}/analytics/throughput
```

🔒 Requires Authentication

**Query Parameters:**

-   `weeks` (optional): Number of weeks (default: 6, max: 52)

**Response (200):**

```json
{
    "data": [
        { "week": "2024-W01", "completed": 5 },
        { "week": "2024-W02", "completed": 8 }
    ]
}
```

### Get Cumulative Flow

```http
GET /boards/{board}/analytics/cumulative-flow
```

🔒 Requires Authentication

**Query Parameters:**

-   `days` (optional): Number of days (default: 30, max: 90)

### Get Assignee Distribution

```http
GET /boards/{board}/analytics/assignees
```

🔒 Requires Authentication

**Response (200):**

```json
{
    "data": [
        {
            "user": { "id": 1, "name": "John Doe" },
            "cards_count": 10
        }
    ]
}
```

---

## Board Templates

### List Templates

```http
GET /board-templates
```

🔒 Requires Authentication

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "name": "Kanban Basic",
            "description": "Simple kanban board",
            "slug": "kanban-basic",
            "visibility": "global",
            "column_count": 4,
            "created_at": "2024-01-01T00:00:00.000Z"
        }
    ]
}
```

### Get Template

```http
GET /board-templates/{template}
```

🔒 Requires Authentication

### Create Template from Board

```http
POST /teams/{team}/board-templates
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "name": "My Template",
    "description": "Template description",
    "board_id": 1
}
```

### Create Template with Columns

```http
POST /teams/{team}/board-templates
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "name": "Custom Template",
    "description": "Custom workflow",
    "columns": [
        { "name": "Backlog", "wip_limit": null },
        { "name": "In Progress", "wip_limit": 5 },
        { "name": "Done", "wip_limit": null }
    ]
}
```

### Delete Template

```http
DELETE /board-templates/{template}
```

🔒 Requires Authentication

### Create Board from Template

```http
POST /teams/{team}/boards/from-template
```

🔒 Requires Authentication

**Request Body:**

```json
{
    "template_id": 1,
    "name": "New Project Board",
    "description": "Board description",
    "color": "#3b82f6"
}
```

---

## Public Boards

### Enable Public Sharing

```http
POST /boards/{board}/public/enable
```

🔒 Requires Authentication

**Response (200):**

```json
{
    "data": {
        "is_public": true,
        "public_token": "uuid-token",
        "public_url": "https://app.com/p/uuid-token"
    },
    "message": "Public sharing enabled"
}
```

### Disable Public Sharing

```http
POST /boards/{board}/public/disable
```

🔒 Requires Authentication

### Regenerate Public Link

```http
POST /boards/{board}/public/regenerate
```

🔒 Requires Authentication

### View Public Board (No Auth Required)

```http
GET /public/boards/{token}
```

**Response (200):**

```json
{
  "data": {
    "id": 1,
    "name": "Project Board",
    "description": "Board description",
    "color": "#3b82f6",
    "columns": [ ... ],
    "labels": [ ... ]
  }
}
```

---

## Health Check

### Basic Health

```http
GET /health
```

**Response (200):**

```json
{
    "status": "ok"
}
```

### App Health (with DB check)

```http
GET /health/app
```

**Response (200):**

```json
{
    "status": "ok",
    "database": "connected",
    "timestamp": "2024-01-01T00:00:00.000Z"
}
```

---

## WebSocket Events (Broadcasting)

### Authentication

```http
POST /broadcasting/auth
```

🔒 Requires Authentication

### Channels

-   `private-board.{boardId}` - Board updates
-   `private-user.{userId}` - User notifications

### Events

| Event              | Channel    | Description                             |
| ------------------ | ---------- | --------------------------------------- |
| `BoardUpdated`     | board.{id} | Board settings changed                  |
| `CardCreated`      | board.{id} | New card created                        |
| `CardUpdated`      | board.{id} | Card updated                            |
| `CardMoved`        | board.{id} | Card moved to different column/position |
| `CardDeleted`      | board.{id} | Card deleted                            |
| `ColumnCreated`    | board.{id} | New column created                      |
| `ColumnUpdated`    | board.{id} | Column updated                          |
| `ColumnDeleted`    | board.{id} | Column deleted                          |
| `CommentCreated`   | board.{id} | New comment added                       |
| `UserNotification` | user.{id}  | Personal notification                   |

---

## Error Responses

### Validation Error (422)

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

### Unauthorized (401)

```json
{
    "message": "Unauthenticated."
}
```

### Forbidden (403)

```json
{
    "message": "This action is unauthorized."
}
```

### Not Found (404)

```json
{
    "message": "Resource not found."
}
```

### Rate Limited (429)

```json
{
    "message": "Too many requests."
}
```

---

## Rate Limits

| Endpoint Group | Limit              |
| -------------- | ------------------ |
| Authentication | 5 requests/minute  |
| Registration   | 3 requests/minute  |
| Public Board   | 60 requests/minute |
| General API    | 60 requests/minute |
