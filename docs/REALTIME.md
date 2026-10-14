# Real-time collaboration

The browser uses Laravel Echo and Pusher.js to connect to Reverb. The API sends synchronous broadcasts to Reverb, and Echo authorizes private/presence subscriptions by sending a Sanctum bearer token to `/api/broadcasting/auth`.

## Local configuration

Backend `.env`:

```dotenv
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=local-syncspace
REVERB_APP_KEY=local-public-key
REVERB_APP_SECRET=replace-with-a-random-local-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

These are illustrative local values. Generate a unique secret for your installation; do not use this example in production. Only the public application key belongs in the browser.

Frontend `.env`:

```dotenv
VITE_API_URL=http://localhost:8000
VITE_BROADCAST_DRIVER=reverb
VITE_REVERB_APP_KEY=local-public-key
VITE_REVERB_HOST=localhost
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http
```

Start the API with `php artisan serve`, Reverb in another terminal with `php artisan reverb:start`, and the client with `yarn dev` from its own repository. Restart Vite after changes. Do not run these foreground processes as if they were sequential setup commands in one terminal.

[config/reverb.php](../config/reverb.php) distinguishes `REVERB_SERVER_HOST`/`REVERB_SERVER_PORT` (listener binding, defaults `0.0.0.0:8080`) from `REVERB_HOST`/`REVERB_PORT` (broadcast destination). Set listener variables explicitly when a reverse proxy or local port layout requires a difference; these optional variables are read in config but absent from `.env.example`.

## Channels and event contracts

[routes/channels.php](../routes/channels.php) authorizes `board.{boardId}`, `team.{teamId}`, `user.{userId}`, and `presence-board.{boardId}`. Presence authorization returns user ID, name, and avatar URL. Keep channel membership checks server-side.

For example, [CardMoved.php](../app/Events/CardMoved.php) broadcasts `CardMoved` on `board.{boardId}`. Echo listens to `.CardMoved` and receives card data, source/destination column IDs, and position. Other events under [app/Events](../app/Events) cover board, column, card, label, comment, team, and notification updates. Client subscription wrappers and constants must match server names and payloads.

The events implement `ShouldBroadcastNow`; a queue worker is not required to deliver these events. Broadcast failures can affect HTTP writes, so start Reverb or disable server broadcasting when developing HTTP-only features.

## Verification and troubleshooting

These checks are instructions, not an audit claim of a live two-browser test:

1. Sign in and inspect the browser's WS connection to the configured Reverb host.
2. Confirm `/api/broadcasting/auth` succeeds for authorized subscriptions; test denial for a nonmember.
3. Open a board in two sessions, move a card, and confirm both update.
4. Verify presence joins/leaves and notification updates separately.

If WS cannot connect, check the listener, browser-reachable host, port, and TLS scheme. If authorization returns 401, check the bearer token and API origin. For 403, check membership and channel names. If connection/authentication succeed but updates do not render, compare event names/payloads and client handlers.

For production, supervise Reverb, configure a WebSocket-capable reverse proxy, and use WSS from HTTPS pages. Set backend broadcast destination and listener independently. Current Reverb configuration permits all origins; review the deployment's origin policy. Scaling requires the configured Redis service only when enabled. No production transport, scaling, or TLS deployment was exercised by this audit.

## Alternate providers

`config/broadcasting.php` contains an Ably connection selected with `BROADCAST_CONNECTION=ably` and server-only `ABLY_KEY`. The client uses a Pusher-compatibility branch selected by `VITE_BROADCAST_DRIVER=ably`. End-to-end compatibility and channel authorization were not verified; treat this as an integration requiring validation, not the recommended production path. Never expose a complete Ably secret in `VITE_*` values.

There is no configured backend `pusher` connection despite an existing comment mentioning it. Do not select it without implementing and testing the required configuration. For HTTP-only work use `BROADCAST_CONNECTION=null`; the client has no equivalent disable selector.
