# Roadmap

## Current validation work

- [Real REST integration validation](rest-validation.md): an opt-in, read-only live smoke test is available. It has not been run against a live account in the current environment.
- [Streaming and RPC architecture design](streaming-rpc-design.md): protocol facts, proposed APIs, state machine, safety semantics and acceptance gates are documented; no Streaming/RPC runtime classes are implemented yet.

## Planned phases

1. Run the live REST smoke test using a dedicated account and local/CI secrets; record the SDK commit, region, UTC time and redacted pass/fail evidence.
2. Complete REST endpoint/path/query auditing and keep path-segment encoding covered by request-construction tests.
3. Prototype a Socket.IO 4.x transport against MetaApi's real endpoint. Prove authentication, `subscribe` → `authenticated` → `synchronize`, heartbeat, reconnect and token redaction before choosing native PHP transport versus a Node bridge.
4. Implement read-only Streaming state synchronization and listeners; then add reconnect and history synchronization correctness.
5. Implement read-only RPC with request ID correlation, bounded waits and normalized errors.
6. Add trading through Streaming/RPC only after dedicated sandbox validation and explicit unknown-outcome handling; never auto-replay side-effecting calls.
7. Add convenience helpers and optional documentation publishing as needed.