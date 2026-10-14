# Architecture and capability boundaries

## Repositories and request flow

The [React client](https://github.com/aardnsyhs/syncspace) sends JSON or multipart requests to this API. [routes/api.php](../routes/api.php) defines endpoints; [bootstrap/app.php](../bootstrap/app.php) adds routing, middleware, and API exception rendering. Controllers coordinate request validation, Eloquent models, resources, policies, services, and broadcast events.

Workspace is the UI term for a team. A user belongs to teams through `team_user`, with a role on each membership. Teams own boards; boards own ordered columns; columns own cards. Cards have comments, board-scoped labels, checklists/items, attachments, and transition records. [database/migrations](../database/migrations) is the authoritative schema; [app/Models](../app/Models) defines relationships.

## Authentication and authorization

Protected endpoints use `auth:sanctum`. Login, OTP verification, and Google code exchange issue bearer tokens; the browser stores its token locally. Registration first returns verification-required metadata, not an authenticated session. [RegisterController.php](../app/Http/Controllers/Auth/RegisterController.php) and [VerifyOTPController.php](../app/Http/Controllers/Auth/VerifyOTPController.php) implement that boundary. Laravel stateful middleware and CSRF-cookie routes also exist; do not assume the client uses cookie-only authentication.

[TeamRole.php](../app/Enums/TeamRole.php) and [policies](../app/Policies) define role behavior:

| Role | Team/member and board management | Card/content editing | View and comment | Delete team |
|---|---|---|---|---|
| Owner | Yes, subject to controller protections | Yes | Yes | Yes |
| Admin | Yes, subject to owner/role protections | Yes | Yes | No |
| Member | No | Yes | Yes | No |
| Viewer | No | No | Yes | No |

This table summarizes role intent, not every endpoint's authorization proof. [TeamMemberController.php](../app/Http/Controllers/TeamMemberController.php) contains additional role protections. [CommentPolicy.php](../app/Policies/CommentPolicy.php) allows a current member to comment and comment authors/managers to delete comments. Run authorization tests when changing policies.

## Collaboration and derived data

[app/Events](../app/Events) contains synchronous `ShouldBroadcastNow` events. [routes/channels.php](../routes/channels.php) checks team membership for board/team channels and user identity for personal channels. See [REALTIME.md](REALTIME.md) for server/client configuration.

[ActivityService.php](../app/Services/ActivityService.php) records supported actions; it is an application activity feed, not evidence of a complete compliance audit trail. [BoardAnalyticsService.php](../app/Services/BoardAnalyticsService.php) uses card transitions and column ordering for throughput, cumulative flow, and cycle/lead time. Analytics treats the final column as completion in relevant calculations; dashboard completion logic also considers flags/keywords. Do not infer universal workflow semantics from these charts.

[NotificationService.php](../app/Services/NotificationService.php) and controller calls provide assignment/comment/movement notifications. [CommentController.php](../app/Http/Controllers/CommentController.php) matches mentions against team member names. A due-soon helper exists but no caller/scheduled job dispatches it in tracked source.

## Known limitations

- MySQL-specific enum alteration, label migration SQL, and analytics queries prevent a claim of PostgreSQL/SQLite portability.
- [UserObserver.php](../app/Observers/UserObserver.php) creates a named team for every new user. [UserService.php](../app/Services/UserService.php) uses `APP_DEFAULT_WORKSPACE_NAME` in its explicit personal-workspace path. New Google users trigger both paths and may receive two teams; the setting does not rename observer-created teams.
- [Board.php](../app/Models/Board.php) checks `public_expires_at`, but public enable/regenerate endpoints do not offer expiry configuration. Public boards have a deliberately limited read-only representation. Expiry validation is implemented; a configurable expiry workflow is not.
- Global templates require explicit [BoardTemplateSeeder.php](../database/seeders/BoardTemplateSeeder.php) initialization, separate from default demo seeding.
- Reverb is the documented local path. The Ably client/server compatibility path exists but lacks end-to-end evidence. A `pusher` connection is mentioned in a config comment but is absent from the actual connection array.
- The board client registers only a pointer sensor; keyboard card movement and full accessibility compliance are unverified.

The joint [OSS readiness audit](https://github.com/aardnsyhs/syncspace/blob/master/docs/OSS_READINESS_AUDIT.md) separates verified tests, source-inspected behavior, and remaining decisions.
