# Real REST integration validation

Status: opt-in integration harness added; it has **not been run against a live MetaApi account in this environment** because no token/account credentials were supplied. Mock tests are not live-server evidence.

## What this checks

`tests/integration/rest-smoke.php` issues a read-only `GET /users/current/accounts/{accountId}/account-information` through the SDK against the real regional REST endpoint. It checks:

- HTTP method, regional host, URL path and `refreshTerminalState=false` query;
- presence of `auth-token` and `Accept: application/json` headers without printing the token;
- actual HTTP 200 response and JSON object decoding;
- documented `balance`, `equity`, `currency` and `server` response fields.

The script does not create, deploy, modify or trade accounts. Optional `--check-404` performs another read-only request using a random UUID and expects the documented 404 for an unknown account.

## Run it

Requirements: PHP 8.2+, installed Composer dependencies, a valid MetaApi auth token with terminal access, and an account that is provisioned and accessible in the selected region.

Set secrets through your shell's environment or a secret manager; do not commit them, paste them into source, or print them in CI logs.

```bash
composer install
export METAAPI_AUTH_TOKEN='…'
export METAAPI_ACCOUNT_ID='…'
export METAAPI_REGION='new-york'
php tests/integration/rest-smoke.php
php tests/integration/rest-smoke.php --check-404
```

`METAAPI_REGION` defaults to `new-york`. Set `METAAPI_TERMINAL_URL` only for a custom/private regional host. The script prints response field names, not account balances or other account values.

Exit codes: `0` = checks passed; `1` = a live request or contract assertion failed; `2` = required environment is missing or invalid.

## Evidence boundaries

- Unit tests with Guzzle mock responses prove request construction and SDK decoding behavior only.
- This script passing proves the listed read-only REST behavior for the token, account and region used in that run; it does not prove all endpoints or all regions.
- A live write/trade test requires a dedicated disposable test account, explicit operator approval and a separate test plan. It is deliberately excluded from this harness.
- Record the UTC run time, SDK commit, region, HTTP status and pass/fail summary in the PR. Never record tokens, account balances, login numbers or full response bodies.

## Official reference

- [Read account information: method, headers, query and 200/401/404 responses](https://metaapi.cloud/docs/client/restApi/api/readTradingTerminalState/readAccountInformation/)
- [Authentication and authorization](https://metaapi.cloud/docs/client/restApi/auth/)