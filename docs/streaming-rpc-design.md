# Streaming and RPC architecture design

Status: **design proposal only**. This document does not claim that streaming or RPC classes have been implemented. Existing `TerminalApi` is REST-only.

## 1. Protocol facts and constraints

- MetaApi streaming is a **Socket.IO protocol**, not a raw WebSocket. A generic WebSocket client alone is not compatible with Socket.IO framing and Engine.IO heartbeat. See [Socket.IO protocol overview](https://socket.io/docs/v4/) and [MetaApi streaming API](https://metaapi.cloud/docs/client/websocket/usingStreamingApi/).
- The regional endpoint is `https://mt-client-api-v1.{region}.agiliumtrade.ai/ws`; the official examples pass `auth-token` in the Socket.IO connection query. Never log that URL/query unredacted.
- A terminal subscription uses a `request` event with `type=subscribe`, `accountId` and a unique `requestId`. Wait for `synchronization` with `type=authenticated` before sending a `type=synchronize` request. See [Subscribe](https://metaapi.cloud/docs/client/websocket/synchronizing/subscribe/) and [Synchronize](https://metaapi.cloud/docs/client/websocket/synchronizing/synchronize/).
- Synchronization is more than connecting a socket: the SDK must maintain a local terminal-state snapshot, apply deltas, track history synchronization and handle `processingError` events. MetaApi explicitly warns that the protocol is complex and recommends using an SDK.
- RPC also uses the Socket.IO API. Official RPC examples connect, wait for synchronization and then issue correlated requests such as `getAccountInformation`, `getPositions` and history queries. RPC requests use the `RPC` application identity; streaming synchronization defaults to `MetaApi`. See [official JavaScript SDK RPC guide](https://github.com/metaapi/metaapi-javascript-sdk/blob/main/docs/metaApi/rpcApi.md).

## 2. Proposed public API

Keep REST, Streaming and RPC as distinct interfaces. The following is proposed API shape, not code that exists today:

```php
$api = new MetaApiClient($token);

$stream = $api->streamingConnection($accountId, region: 'new-york');
$stream->connect();
$stream->waitSynchronized(timeoutSeconds: 120);
$state = $stream->terminalState();
$stream->subscribeToMarketData('EURUSD');
$stream->disconnect();

$rpc = $api->rpcConnection($accountId, region: 'new-york');
$rpc->connect();
$rpc->waitSynchronized(timeoutSeconds: 120);
$information = $rpc->getAccountInformation();
$positions = $rpc->getPositions();
$rpc->disconnect();
```

Use explicit `connect`, bounded waits and `disconnect`; do not hide long-lived socket behavior inside a short REST method. Document that the first implementation targets long-running PHP CLI/worker processes. Traditional short-lived PHP-FPM requests are a poor fit for maintaining a streaming connection.

## 3. Internal components

```text
MetaApiClient
  ├── TerminalApi (existing REST client)
  ├── StreamingConnection
  │     └── Shared Socket.IO transport + protocol dispatcher
  │            └── SynchronizationEngine
  │                   ├── TerminalState (account info, positions, orders, specs, prices)
  │                   ├── HistoryStorage (orders/deals + completion flags)
  │                   └── SynchronizationListener callbacks
  └── RpcConnection
        └── Shared Socket.IO transport + requestId correlation
               ├── response → resolve the matching request
               └── processingError → normalized SDK exception
```

Suggested boundaries:

- `SocketIoTransport`: connect, emit event, receive the next event with a deadline, disconnect and expose transport state. No MetaApi-specific state logic.
- `ProtocolDispatcher`: parse `synchronization`, `response`, `processingError` and transport events; reject malformed payloads; maintain unique request IDs for each connection.
- `SynchronizationEngine`: subscribe/authenticate, send synchronize request, apply initial snapshots and deltas, keep readiness flags, and replay desired subscriptions after reconnect.
- `TerminalState` and `HistoryStorage`: in-memory first release, with a storage interface for history persistence later. Do not persist tokens by default.
- `RpcConnection`: public read/query/trade methods built on request correlation, but not coupled to the REST `Http` class.

## 4. Connection and synchronization state machine

```text
DISCONNECTED → CONNECTING → SUBSCRIBING → AUTHENTICATED
                                      ↓
                              SYNCHRONIZING → SYNCHRONIZED
                                      ↓               ↓
                                  FAILED ←──── DISCONNECTING
                                      ↓
                              RECONNECT_WAIT → CONNECTING
```

Required invariants:

1. Generate a unique request ID for every protocol request; IDs must not be reused within one API connection.
2. Do not send `synchronize` before the `authenticated` synchronization event.
3. Distinguish terminal-state readiness from history readiness. Expose separate flags/waits for the terminal snapshot and completion of both history-order and deal synchronization.
4. A reconnect invalidates the prior connection's request correlation and readiness state. Re-authenticate, synchronize again, restore market-data subscriptions and only then mark state ready.
5. If the server indicates data is not synchronized, surface a typed error/state; never return a stale snapshot as if it were current.
6. Bound `waitSynchronized`, RPC calls and shutdown with deadlines. On timeout, clean up pending waiters.

## 5. RPC semantics and safety

- Correlate every request with its `requestId`; return only the response for that request and preserve concurrent/unrelated events in the receive queue.
- The first synchronous PHP implementation should serialize in-flight RPC requests per connection unless a proven event-loop implementation supports safe multiplexing. PHP callers can use multiple connections when independent concurrency is needed.
- Normalize server `processingError` payloads into typed SDK exceptions while preserving MetaApi error fields.
- Read-only operations may be retried only under an explicit, documented policy and after re-synchronization.
- **Never automatically replay a trade or other side-effecting request after a timeout/disconnect.** The remote server may have executed it while the response was lost. Surface an “outcome unknown” error and require caller reconciliation. Do not assume `clientId` is an idempotency key unless official semantics confirm it.
- Keep existing REST convenience methods available; do not silently switch a REST call to RPC.

## 6. Transport decision

Run a small protocol-compatibility spike before choosing a production dependency:

1. Evaluate [`elephantio/elephant.io`](https://github.com/ElephantIO/elephant.io), which advertises Socket.IO 4.x support, as an optional native PHP transport adapter.
2. Prove the MetaApi endpoint handshake, token query, `request` emission, named event reception, heartbeat behavior and reconnect flow with a dedicated test account.
3. Compare it with a Node bridge that delegates to the official JavaScript SDK. The bridge has higher operational cost (Node runtime/process lifecycle) but can reuse MetaApi's mature synchronization implementation.
4. Do not implement raw WebSocket framing or hand-roll the full synchronization protocol. If the native adapter cannot pass the compatibility tests reliably, use the bridge as an optional adapter or defer the feature.

Recommendation: keep a transport interface and prototype native Socket.IO first; make the native-vs-bridge choice only after a live compatibility test. Do not add a hard runtime dependency or promise production streaming support until that gate passes.

## 7. Delivery phases

| Phase | Deliverable | Acceptance gate |
|---|---|---|
| S0 | Socket.IO transport spike | Live connect/authenticated event and clean disconnect; token redaction; protocol fixtures |
| S1 | Streaming MVP, read-only | Subscribe → authenticate → synchronize; local account/position/order state; listeners; quote subscribe/unsubscribe |
| S2 | Reconnect and history correctness | Backoff with jitter, state invalidation, re-sync, duplicate-event handling, order/deal history completion flags |
| R1 | RPC read-only MVP | Account information, positions, orders, server time and history queries; requestId correlation; timeouts and processing errors |
| R2 | RPC market data | Symbol/specification/price calls and subscription lifecycle |
| R3 | Trading via RPC/Streaming | Only after dedicated sandbox validation, trade-result normalization and unknown-outcome handling; no automatic replay |

## 8. Test strategy

- Unit tests with a fake transport for state transitions, request IDs, out-of-order responses, `processingError`, timeout cleanup and disconnect handling.
- Protocol fixture tests based on documented `subscribe`, `authenticated`, `synchronize`, response and history-completion events.
- Live, opt-in integration tests using a dedicated test account: connect, synchronize, read account information/positions, subscribe/unsubscribe to a liquid symbol and disconnect.
- Fault injection: drop the connection during sync, during an RPC read and after sending a trade request but before receiving its response. The last case must prove that the SDK reports an unknown outcome and does not resend.
- No trading operations in ordinary CI. Mock tests do not count as evidence of live protocol compatibility.

## Official references

- [MetaApi: Using streaming API](https://metaapi.cloud/docs/client/websocket/usingStreamingApi/)
- [MetaApi: Subscribe request](https://metaapi.cloud/docs/client/websocket/synchronizing/subscribe/)
- [MetaApi: Synchronize request](https://metaapi.cloud/docs/client/websocket/synchronizing/synchronize/)
- [MetaApi: WebSocket error handling](https://metaapi.cloud/docs/client/websocket/errors/)
- [MetaApi JavaScript SDK: Streaming API](https://github.com/metaapi/metaapi-javascript-sdk/blob/main/docs/metaApi/streamingApi.md)
- [MetaApi JavaScript SDK: RPC API](https://github.com/metaapi/metaapi-javascript-sdk/blob/main/docs/metaApi/rpcApi.md)
- [Elephant.IO Socket.IO client](https://github.com/ElephantIO/elephant.io)
- [Socket.IO is not raw WebSocket](https://socket.io/docs/v4/)