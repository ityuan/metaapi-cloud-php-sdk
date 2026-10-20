# API coverage baseline

Audit date: 2026-10-20
Repository baseline: main at 07d850175da71d2e46e81d55bef2010277ad0465
Method: static comparison of repository source/tests against public MetaApi endpoint documentation. This is a maintainable baseline, not a claim that every endpoint in every official Swagger specification has been exhaustively enumerated.

## Status definitions

- **Implemented and tested**: source method exists and a repository test directly asserts its request path or behavior.
- **Implemented, verification missing**: source method exists, but no direct test was found during this audit.
- **Partial**: a feature family has some implementation, but its full official endpoint/parameter coverage is not yet confirmed.
- **Not implemented**: no corresponding SDK implementation was found in the inspected source tree.

The tests currently use Guzzle mock responses. Tested does not imply live-account integration testing.

## Official sources checked

- [Provisioning API overview](https://metaapi.cloud/docs/provisioning/) and endpoint docs for [account list](https://metaapi.cloud/docs/provisioning/api/account/readAccounts/), [account create](https://metaapi.cloud/docs/provisioning/api/account/createAccount/), [account deploy](https://metaapi.cloud/docs/provisioning/api/account/deployAccount/), [profile list](https://metaapi.cloud/docs/provisioning/api/provisioningProfile/readProvisioningProfiles/), [profile detail](https://metaapi.cloud/docs/provisioning/api/provisioningProfile/readProvisioningProfile/), [profile create](https://metaapi.cloud/docs/provisioning/api/provisioningProfile/createNewProvisioningProfile/), [profile update](https://metaapi.cloud/docs/provisioning/api/provisioningProfile/updateProvisioningProfile/), and [profile delete](https://metaapi.cloud/docs/provisioning/api/provisioningProfile/deleteProvisioningProfile/).
- [Terminal REST/WebSocket API overview](https://metaapi.cloud/docs/client/) and [REST overview](https://metaapi.cloud/docs/client/restApi/overview/).
- [CopyFactory API overview](https://metaapi.cloud/docs/copyfactory/).
- [MetaStats API overview](https://metaapi.cloud/docs/metastats/).
- [MT Manager API overview](https://metaapi.cloud/docs/manager/).
- [Risk Management API overview](https://metaapi.cloud/docs/risk-management/).

Where an endpoint-specific official page is not linked, the row is an implementation/test inventory based on SDK source and must be treated as partial pending Swagger-level confirmation.

## Provisioning / account management

| Official endpoint or endpoint group | SDK method | Test evidence | Status | Notes |
|---|---|---|---|---|
| GET /users/current/accounts | Account::accounts() | tests/AccountManagementTest.php | Implemented and tested | Official endpoint page linked above; filters and API version tested. |
| GET /users/current/accounts/{accountId} | Account::readById() | Added in this P0 branch | Implemented and tested | Detail endpoint; path segment encoding regression test. |
| POST /users/current/accounts | Account::create() | tests/AccountManagementTest.php | Implemented and tested | Transaction ID header and 201/202 metadata; now exposes request ID. |
| PUT /users/current/accounts/{accountId} | Account::update() | Added in this P0 branch | Implemented and tested | Update payload and encoded path tested. |
| POST /users/current/accounts/{accountId}/deploy | Account::deploy() | Added in this P0 branch | Implemented and tested | Query flag tested. |
| POST /users/current/accounts/{accountId}/undeploy | Account::undeploy() | Added in this P0 branch | Implemented and tested | Query flag tested. |
| POST /users/current/accounts/{accountId}/redeploy | Account::redeploy() | Added in this P0 branch | Implemented and tested | Encoded path tested. |
| DELETE /users/current/accounts/{accountId} | Account::delete() | tests/AccountManagementTest.php | Implemented and tested | Delete query tested. |
| PUT /users/current/accounts/{accountId}/configuration-link | Account::createConfigurationLink() | Added in this P0 branch | Implemented and tested | TTL query tested. |
| Account replicas list/detail/create/update/deploy/undeploy/redeploy/delete/reliability | AccountReplica::* | tests/AccountManagementTest.php | Partial | Lifecycle calls and encoded segments covered; still needs official Swagger endpoint-by-endpoint parameter audit. |
| GET /users/current/provisioning-profiles | ProvisioningProfile::provisioningProfiles() | tests/ProvisioningProfileTest.php | Implemented and tested | Filters and API version tested. |
| GET /users/current/provisioning-profiles/{id} | ProvisioningProfile::provisioningProfile() | Added in this P0 branch | Implemented and tested | Detail response and encoded path tested. |
| POST /users/current/provisioning-profiles | ProvisioningProfile::createProvisioningProfile() | tests/ProvisioningProfileTest.php | Implemented and tested | Official endpoint page linked above. |
| PUT /users/current/provisioning-profiles/{id} | ProvisioningProfile::updateProvisioningProfile() | Added in this P0 branch | Implemented and tested | Payload and encoded path tested. |
| PUT /users/current/provisioning-profiles/{id}/{fileName} | ProvisioningProfile::uploadProvisioningProfileFile() | Added in this P0 branch | Implemented and tested | Multipart upload and encoded path tested. |
| DELETE /users/current/provisioning-profiles/{id} | ProvisioningProfile::deleteProvisioningProfile() | tests/ProvisioningProfileTest.php | Implemented and tested | Official endpoint page linked above. |
| Demo accounts, Expert Advisors, quotas, known MT servers | DemoAccount, ExpertAdvisor, Quota, MtServer | Corresponding tests in tests/ | Partial | Methods exist and many are tested; complete official endpoint inventory still pending. |

## Terminal REST

| Endpoint group | SDK method | Test evidence | Status | Notes |
|---|---|---|---|---|
| Account information, positions and orders list/detail, server time | Resources/Terminal/State | tests/TerminalApiTest.php | Implemented and tested | Core paths are asserted. |
| History orders/deals by ticket, position and time range | Resources/Terminal/History | tests/TerminalApiTest.php | Implemented and tested | Query/path encoding tested. |
| Symbols, specifications, current price/candles/tick/order book, historical candles/ticks | Resources/Terminal/MarketData | tests/TerminalApiTest.php | Partial | Core helpers tested; full parameter/version parity with official REST reference not yet verified. |
| Trade request, convenience order methods, modify/close/cancel | Resources/Terminal/Trading | tests/TerminalApiTest.php | Partial | Generic trade request tested; every convenience action and failure mode not independently tested. |
| Margin calculation and credits usage | Margin, Credits | tests/TerminalApiTest.php | Implemented and tested | Mock request coverage. |
| Socket.IO streaming connection, state synchronization and live market subscriptions | No implementation found | None | Not implemented | Roadmap also lists real-time streaming/WebSocket support as future work. |
| Persistent RPC connection lifecycle | No implementation found | None | Not implemented | REST trade request is not equivalent to streaming RPC. |

## CopyFactory

| Endpoint group | SDK method | Test evidence | Status | Notes |
|---|---|---|---|---|
| Strategies and subscribers list/detail/update/delete; subscription delete | Resources/Copyfactory/Configuration | New configuration CRUD test plus tests/CopyFactoryTest.php | Partial | Core CRUD request paths tested in this branch; official Swagger-level completeness still pending. |
| Portfolio strategies and members | Configuration::portfolio* | tests/CopyFactoryTest.php | Implemented and tested | Current tests cover the principal portfolio CRUD paths. |
| Webhooks list/create/update/delete | Resources/Copyfactory/Webhooks | tests/CopyFactoryTest.php | Implemented and tested | Mock path/query assertions. |
| Transactions/history streams | Resources/Copyfactory/History | tests/CopyFactoryTest.php | Implemented and tested | Four paths tested. |
| Signals, external signals, stopouts and resynchronize | Resources/Copyfactory/Trading | tests/CopyFactoryTest.php | Implemented and tested | Core paths tested. |
| User and strategy logs/streams | Resources/Copyfactory/Logs | tests/CopyFactoryTest.php | Implemented and tested | Core paths tested. |

## MetaStats, MT Manager and Risk Management

| Module | SDK methods | Test evidence | Status | Notes |
|---|---|---|---|---|
| MetaStats metrics, historical trades, open trades, reset | MetaStats, Resources/Metastats/Metrics | tests/MetaStatsTest.php | Partial | Core methods tested; asynchronous computation and full parameter parity need review. |
| MT4/MT5 Manager accounts, groups, permissions, balance, authentication logs | ManagerApi, Resources/Manager/* | tests/ManagerApiTest.php | Partial | Many methods covered; full version-specific endpoint inventory not yet proven. |
| Risk trackers, events, stream, statistics, equity chart | RiskManagementApi, Resources/RiskManagement/Trackers | tests/RiskManagementApiTest.php | Partial | Main methods tested; API-version policy and official endpoint parity need review. |

## P0 reliability checks

| Concern | Current handling after this branch | Evidence |
|---|---|---|
| Account creation transaction ID | Generated ID is exposed through ActionResponse::transactionId(); existing create($data, $transactionId) signature remains compatible. | tests/AccountManagementTest.php |
| HTTP timeouts | Default Guzzle client has 30s total timeout and 10s connect timeout; optional client options can override. Injected clients keep their own config. | tests/HttpTest.php |
| Path parameter encoding | Shared Resources/Path::segment() is used by account, replica, provisioning profile, CopyFactory configuration and webhook paths. | Account, replica, provisioning and CopyFactory tests |
| JSON scalar type safety | JSON scalar bodies no longer leak bool/int/null values through array|string|null return signatures; Retry-After is only appended to array bodies. | tests/HttpTest.php |
| Automatic retries | No automatic retries are performed; side-effecting create/trade requests must not be resent blindly after transport failures. | docs/error-handling.md |

## Remaining audit work

1. Import or enumerate the complete official Swagger/OpenAPI endpoint definitions for each service, including optional query/body parameters and API versions.
2. Compare every official operation against a concrete SDK method and direct test; mark any missing operation as not implemented only after the official operation is verified.
3. Add live integration tests in a dedicated MetaApi test account for 202/polling, permissions, rate limits and terminal synchronization.
4. Review path encoding in all remaining resource classes, not only the P0-focused classes updated here.