# MetaApi API coverage baseline

- Audit date: 2026-10-20
- Repository baseline: `main` commit `07d850175da71d2e46e81d55bef2010277ad0465`
- Audit method: source and Pest test inspection, compared with the linked MetaApi official endpoint documentation.
- Specification limitation: no stable, downloadable raw OpenAPI/Swagger document was verified during this pass. The official docs pages below describe individual API endpoints and link to Swagger, but this table is a traceable baseline, **not a claim that every official endpoint has been exhaustively enumerated**. Recheck the official Swagger/API docs before declaring 100% coverage.

## Status definitions

- **Implemented and tested**: an SDK method exists and a Pest test directly exercises the method or its request behavior.
- **Implemented, validation missing**: an SDK method exists, but no direct test for that method was found.
- **Partial**: only part of a related API family or method variants are represented, or the official endpoint mapping needs confirmation.
- **Not implemented**: no corresponding implementation was found in the audited source tree.

The route strings below are the canonical paths confirmed by the linked official endpoint pages where such a page is linked; where only a module landing page is cited, the route is the one implemented in source and per-operation official Swagger confirmation remains pending. Mocked HTTP tests verify SDK request construction and response handling, not successful calls against a live MetaApi account.

## Provisioning / account management

Official reference: [Provisioning API](https://metaapi.cloud/docs/provisioning/), [account create](https://metaapi.cloud/docs/provisioning/api/account/createAccount/), [account read](https://metaapi.cloud/docs/provisioning/api/account/readAccount/), [account list](https://metaapi.cloud/docs/provisioning/api/account/readAccounts/), [account update](https://metaapi.cloud/docs/provisioning/api/account/updateAccount/), [deploy](https://metaapi.cloud/docs/provisioning/api/account/deployAccount/), [configuration link](https://metaapi.cloud/docs/provisioning/api/account/createConfigurationLink/), [enable CopyFactory](https://metaapi.cloud/docs/provisioning/api/account/enableCopyFactoryApi/).

| Official HTTP endpoint | SDK class / method | Test evidence | Status / notes |
|---|---|---|---|
| `GET /users/current/accounts` | `Account::accounts()` | `AccountManagementTest::reads accounts with filters and api version header` | Implemented and tested |
| `GET /users/current/accounts/{accountId}` | `Account::readById()` | `AccountLifecycleTest::reads an account by id and encodes the path segment` | Implemented and tested |
| `POST /users/current/accounts` | `Account::create()` | `AccountManagementTest::creates accounts with transaction id headers`; `HttpReliabilityTest::exposes the generated transaction id for accepted account creation` | Implemented and tested; transaction ID now exposed by `ActionResponse::transactionId()` |
| `PUT /users/current/accounts/{accountId}` | `Account::update()` | `AccountLifecycleTest::updates an account and sends the request payload` | Implemented and tested |
| `POST /users/current/accounts/{accountId}/deploy` | `Account::deploy()` | `AccountLifecycleTest::deploys an account with replica scope query` | Implemented and tested |
| `POST /users/current/accounts/{accountId}/undeploy` | `Account::undeploy()` | `AccountLifecycleTest::undeploys an account with replica scope query` | Implemented and tested |
| `POST /users/current/accounts/{accountId}/redeploy` | `Account::redeploy()` | `AccountLifecycleTest::redeploys an account with replica scope query` | Implemented and tested |
| `DELETE /users/current/accounts/{accountId}` | `Account::delete()` | `AccountManagementTest::deletes accounts using the account endpoint` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/examples/{platform}` | `Account::generateCodeSample()` | No direct test found | Implemented, validation missing |
| `POST /users/current/accounts/{accountId}/enable-account-features` | `Account::enableFeatures()` | `AccountManagementTest::enables account features` | Implemented and tested |
| `POST /users/current/accounts/{accountId}/enable-copy-factory-api` | `Account::enableCopyFactoryApi()` | `AccountLifecycleTest::enables CopyFactory API with role and slot payload` | Implemented and tested; official endpoint confirmed |
| `PUT /users/current/accounts/{accountId}/configuration-link` | `Account::createConfigurationLink()` | `AccountLifecycleTest::creates a configuration link with ttl query` | Implemented and tested |
| `PUT /users/current/accounts/{accountId}/credentials` | No SDK method found | None | **Not implemented**; official [Configure trading account](https://metaapi.cloud/docs/provisioning/api/account/configureTradingAccount/) endpoint can configure draft account credentials or change a configured account password |

## Account replicas

Official reference: [Provisioning API](https://metaapi.cloud/docs/provisioning/), [create account replica](https://metaapi.cloud/docs/provisioning/api/accountReplica/createAccountReplica/).

| Official HTTP endpoint | SDK class / method | Test evidence | Status / notes |
|---|---|---|---|
| `GET /users/current/accounts/{accountId}/replicas` | `AccountReplica::replicas()` | `AccountReplicaLifecycleTest::lists account replicas` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/replicas/{replicaId}` | `AccountReplica::replica()` | `AccountReplicaLifecycleTest::reads a replica by id with encoded path segments` | Implemented and tested |
| `POST /users/current/accounts/{accountId}/replicas` | `AccountReplica::createReplica()` | `AccountManagementTest::creates account replicas` | Implemented and tested; generated transaction ID exposed on action response |
| `PUT /users/current/accounts/{accountId}/replicas/{replicaId}` | `AccountReplica::updateReplica()` | `AccountReplicaLifecycleTest::updates an account replica` | Implemented and tested |
| `POST /users/current/accounts/{accountId}/replicas/{replicaId}/deploy` | `AccountReplica::deployReplica()` | `AccountReplicaLifecycleTest::deploys an account replica` | Implemented and tested |
| `POST /users/current/accounts/{accountId}/replicas/{replicaId}/undeploy` | `AccountReplica::undeployReplica()` | `AccountReplicaLifecycleTest::undeploys an account replica` | Implemented and tested |
| `POST /users/current/accounts/{accountId}/replicas/{replicaId}/redeploy` | `AccountReplica::redeployReplica()` | `AccountReplicaLifecycleTest::redeploys an account replica` | Implemented and tested |
| `DELETE /users/current/accounts/{accountId}/replicas/{replicaId}` | `AccountReplica::deleteReplica()` | `AccountReplicaLifecycleTest::deletes an account replica` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/replicas/{replicaId}/examples/{platform}` | `AccountReplica::generateReplicaCodeSample()` | `AccountReplicaLifecycleTest::generates a replica code sample with encoded platform segment` | Implemented and tested |
| `POST /users/current/accounts/{accountId}/replicas/{replicaId}/increase-reliability` | `AccountReplica::increaseReplicaReliability()` | `AccountReplicaLifecycleTest::increases account replica reliability` | Implemented and tested |

## Provisioning profiles, demo accounts, expert advisors, quotas

Official reference: [Provisioning API](https://metaapi.cloud/docs/provisioning/), [create provisioning profile](https://metaapi.cloud/docs/provisioning/api/provisioningProfile/createNewProvisioningProfile/), [read provisioning profiles](https://metaapi.cloud/docs/provisioning/api/provisioningProfile/readProvisioningProfiles/).

| Official HTTP endpoint | SDK class / method | Test evidence | Status / notes |
|---|---|---|---|
| `GET /users/current/provisioning-profiles` | `ProvisioningProfile::provisioningProfiles()` | `ProvisioningProfileTest::reads provisioning profiles with filters` | Implemented and tested |
| `GET /users/current/provisioning-profiles/{profileId}` | `ProvisioningProfile::provisioningProfile()` | `ProvisioningProfileLifecycleTest::reads a provisioning profile by id with path encoding` | Implemented and tested |
| `POST /users/current/provisioning-profiles` | `ProvisioningProfile::createProvisioningProfile()` | `ProvisioningProfileTest::creates provisioning profiles` | Implemented and tested |
| `PUT /users/current/provisioning-profiles/{profileId}` | `ProvisioningProfile::updateProvisioningProfile()` | `ProvisioningProfileLifecycleTest::updates a provisioning profile` | Implemented and tested |
| `PUT /users/current/provisioning-profiles/{profileId}/{fileName}` | `ProvisioningProfile::uploadProvisioningProfileFile()` | `ProvisioningProfileLifecycleTest::uploads a provisioning profile file using encoded path segments and multipart content` | Implemented and tested |
| `DELETE /users/current/provisioning-profiles/{profileId}` | `ProvisioningProfile::deleteProvisioningProfile()` | `ProvisioningProfileTest::deletes provisioning profiles` | Implemented and tested |
| `POST /users/current/provisioning-profiles/{profileId}/mt4-demo-accounts` | `DemoAccount::createMT4DemoAccount()` | `DemoAccountTest` | Implemented and tested |
| `POST /users/current/provisioning-profiles/{profileId}/mt5-demo-accounts` | `DemoAccount::createMT5DemoAccount()` | `DemoAccountTest` | Implemented and tested |
| `POST /users/current/provisioning-profiles/{profileId}/mt4-live-accounts` | No SDK method found | None | **Not implemented**; official [MT4 live account creation](https://metaapi.cloud/docs/provisioning/api/generateAccount/createMT4LiveAccount/) endpoint |
| `POST /users/current/provisioning-profiles/{profileId}/mt5-live-accounts` | No SDK method found | None | **Not implemented**; official [MT5 live account creation](https://metaapi.cloud/docs/provisioning/api/generateAccount/createMT5LiveAccount/) endpoint |
| `GET /users/current/accounts/{accountId}/expert-advisors` | `ExpertAdvisor::expertAdvisors()` | `ExpertAdvisorTest::lists expert advisors` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/expert-advisors/{expertId}` | `ExpertAdvisor::expertAdvisor()` | `ExpertAdvisorTest::reads a single expert advisor` | Implemented and tested |
| `PUT /users/current/accounts/{accountId}/expert-advisors/{expertId}` | `ExpertAdvisor::updateExpertAdvisor()` | `ExpertAdvisorTest::updates an expert advisor` | Implemented and tested |
| `PUT /users/current/accounts/{accountId}/expert-advisors/{expertId}/file` | `ExpertAdvisor::uploadExpertAdvisorFile()` | `ExpertAdvisorTest::uploads an expert advisor file` | Implemented and tested |
| `DELETE /users/current/accounts/{accountId}/expert-advisors/{expertId}` | `ExpertAdvisor::deleteExpertAdvisor()` | `ExpertAdvisorTest::deletes an expert advisor` | Implemented and tested |
| `GET /users/current/quotas` | `Quota::quotas()` | `QuotaTest::gets user quotas` | Implemented and tested |
| `GET /users/current/quota-update-requests` | `Quota::quotaUpdateRequests()` | `QuotaTest::gets quota update requests` | Implemented and tested |
| `PATCH /users/current/regions/{region}/quotas` | `Quota::requestRegionQuotaUpdate()` | `QuotaTest::requests a region quota update` | Implemented and tested |
| `GET /known-mt-servers/{version}/search` | `MtServer::knownTradingServers()` | `MtServerTest::searches known trading servers by version and query` | Implemented and tested |
| `POST /accounts/register` | No SDK method found | None | Not implemented in audited source; include only if self-hosted account registration is in scope |

## Terminal REST API

Official reference: [Terminal REST API overview](https://metaapi.cloud/docs/client/restApi/overview/), [Terminal API documentation](https://metaapi.cloud/docs/client/).

| Official HTTP endpoint | SDK class / method | Test evidence | Status / notes |
|---|---|---|---|
| `GET /users/current/accounts/{accountId}/account-information` | `State::accountInformation()` | `TerminalApiTest::reads trading terminal state` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/positions` | `State::positions()` | `TerminalApiTest::reads trading terminal state` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/positions/{positionId}` | `State::position()` | `TerminalApiTest::reads trading terminal state` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/orders` | `State::orders()` | `TerminalApiTest::reads trading terminal state` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/orders/{orderId}` | `State::order()` | `TerminalApiTest::reads trading terminal state` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/server-time` | `State::serverTime()` | `TerminalApiTest::reads trading terminal state` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/history-orders/ticket/{ticket}` | `History::historyOrdersByTicket()` | `TerminalApiTest::retrieves historical trading data` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/history-orders/position/{positionId}` | `History::historyOrdersByPosition()` | `TerminalApiTest::retrieves historical trading data` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/history-orders/time/{startTime}/{endTime}` | `History::historyOrdersByTimeRange()` | `TerminalApiTest::retrieves historical trading data` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/history-deals/ticket/{ticket}` | `History::dealsByTicket()` | `TerminalApiTest::retrieves historical trading data` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/history-deals/position/{positionId}` | `History::dealsByPosition()` | `TerminalApiTest::retrieves historical trading data` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/history-deals/time/{startTime}/{endTime}` | `History::dealsByTimeRange()` | `TerminalApiTest::retrieves historical trading data` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/symbols` | `MarketData::symbols()` | `TerminalApiTest::retrieves market data` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/symbols/{symbol}/specification` | `MarketData::symbolSpecification()` | `TerminalApiTest::retrieves market data` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/symbols/{symbol}/current-price` | `MarketData::symbolPrice()` | `TerminalApiTest::retrieves market data` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/symbols/{symbol}/current-candles/{timeframe}` | `MarketData::candle()` | `TerminalApiTest::retrieves market data` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/symbols/{symbol}/current-tick` | `MarketData::tick()` | `TerminalApiTest::retrieves market data` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/symbols/{symbol}/current-book` | `MarketData::orderBook()` | `TerminalApiTest::retrieves market data` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/historical-market-data/symbols/{symbol}/timeframes/{timeframe}/candles` | `MarketData::historicalCandles()` | `TerminalApiTest::retrieves market data` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/historical-market-data/symbols/{symbol}/ticks` | `MarketData::historicalTicks()` | `TerminalApiTest::retrieves market data` | Implemented and tested |
| `POST /users/current/accounts/{accountId}/trade` | `Trading::trade()` | `TerminalApiTest::calculates margin, trades and retrieves CPU credit usage` | Implemented and tested |
| `POST /users/current/accounts/{accountId}/trade` | `Trading::createMarketBuyOrder()` | No direct test found | Implemented, validation missing |
| `POST /users/current/accounts/{accountId}/trade` | `Trading::createMarketSellOrder()` | `TerminalApiTest::calculates margin, trades and retrieves CPU credit usage` | Implemented and tested |
| `POST /users/current/accounts/{accountId}/trade` | `Trading::createLimitBuyOrder()`, `createLimitSellOrder()` | No direct test found | Implemented, validation missing |
| `POST /users/current/accounts/{accountId}/trade` | `Trading::createStopBuyOrder()`, `createStopSellOrder()` | No direct test found | Implemented, validation missing |
| `POST /users/current/accounts/{accountId}/trade` | `Trading::createStopLimitBuyOrder()`, `createStopLimitSellOrder()` | No direct test found | Implemented, validation missing |
| `POST /users/current/accounts/{accountId}/trade` | `Trading::modifyPosition()`, `closePosition()`, `closePositionPartially()`, `closePositionsBySymbol()`, `closeBy()`, `modifyOrder()`, `cancelOrder()` | No direct test found for each helper | Partial: common endpoint works; individual action payloads need tests |
| `POST /users/current/accounts/{accountId}/calculate-margin` | `Margin::calculate()` | `TerminalApiTest` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/credits` | `Credits::usage()` | `TerminalApiTest` | Implemented and tested |

## CopyFactory

Official reference: [CopyFactory API documentation](https://metaapi.cloud/docs/copyfactory/).

| Official HTTP endpoint | SDK class / method | Test evidence | Status / notes |
|---|---|---|---|
| `GET /users/current/configuration/unused-strategy-id` | `Configuration::generateStrategyId()` | `CopyFactoryTest::generates a strategy id without listing strategies by default` | Implemented and tested |
| `GET /users/current/configuration/strategies` | `Configuration::strategies()` | `CopyFactoryTest::only lists strategies when reuse is explicitly enabled` | Implemented and tested |
| `GET /users/current/configuration/strategies` with `api-version: 2` | `Configuration::strategiesV2()` | No direct V2 test found | Implemented, validation missing |
| `GET /users/current/configuration/strategies/{strategyId}` | `Configuration::strategy()` | `CopyFactoryConfigurationTest::reads a strategy by id with path encoding` | Implemented and tested |
| `PUT /users/current/configuration/strategies/{strategyId}` | `Configuration::updateStrategy()` | `CopyFactoryConfigurationTest::updates a strategy and sends its payload` | Implemented and tested |
| `DELETE /users/current/configuration/strategies/{strategyId}` | `Configuration::removeStrategy()` | `CopyFactoryConfigurationTest::removes a strategy` | Implemented and tested |
| `GET /users/current/configuration/portfolio-strategies` | `Configuration::portfolioStrategies()` | `CopyFactoryTest::supports portfolio strategy endpoints` | Implemented and tested |
| `GET /users/current/configuration/portfolio-strategies/{portfolioId}` | `Configuration::portfolioStrategy()` | `CopyFactoryTest::supports portfolio strategy endpoints` | Implemented and tested |
| `PUT /users/current/configuration/portfolio-strategies/{portfolioId}` | `Configuration::updatePortfolioStrategy()` | `CopyFactoryTest::supports portfolio strategy endpoints` | Implemented and tested |
| `DELETE /users/current/configuration/portfolio-strategies/{portfolioId}` | `Configuration::removePortfolioStrategy()` | `CopyFactoryTest::supports portfolio strategy endpoints` | Implemented and tested |
| `DELETE /users/current/configuration/portfolio-strategies/{portfolioId}/members/{strategyId}` | `Configuration::removePortfolioStrategyMember()` | `CopyFactoryTest::supports portfolio strategy endpoints` | Implemented and tested |
| `GET /users/current/configuration/subscribers` | `Configuration::subscribers()` | `CopyFactoryConfigurationTest::lists subscribers with filters` | Implemented and tested |
| `GET /users/current/configuration/subscribers` with `api-version: 2` | `Configuration::subscribersV2()` | `CopyFactoryConfigurationTest::lists subscribers using API version 2` | Implemented and tested |
| `GET /users/current/configuration/subscribers/{subscriberId}` | `Configuration::subscriber()` | `CopyFactoryConfigurationTest::reads a subscriber by id with path encoding` | Implemented and tested |
| `PUT /users/current/configuration/subscribers/{subscriberId}` | `Configuration::updateSubscriber()` | `CopyFactoryConfigurationTest::updates a subscriber and sends its payload` | Implemented and tested |
| `DELETE /users/current/configuration/subscribers/{subscriberId}` | `Configuration::removeSubscriber()` | `CopyFactoryConfigurationTest::removes a subscriber` | Implemented and tested |
| `DELETE /users/current/configuration/subscribers/{subscriberId}/subscriptions/{strategyId}` | `Configuration::deleteSubscription()` | `CopyFactoryConfigurationTest::deletes a subscription using encoded subscriber and strategy ids` | Implemented and tested |
| `GET /users/current/configuration/strategies/{strategyId}/webhooks` | `Webhooks::list()` | `CopyFactoryTest::supports webhook endpoints` | Implemented and tested |
| `POST /users/current/configuration/strategies/{strategyId}/webhooks` | `Webhooks::create()` | `CopyFactoryTest::supports webhook endpoints` | Implemented and tested |
| `PATCH /users/current/configuration/strategies/{strategyId}/webhooks/{webhookId}` | `Webhooks::update()` | `CopyFactoryTest::supports webhook endpoints` | Implemented and tested |
| `DELETE /users/current/configuration/strategies/{strategyId}/webhooks/{webhookId}` | `Webhooks::remove()` | `CopyFactoryTest::supports webhook endpoints` | Implemented and tested |
| `GET /users/current/provided-transactions` | `History::providedTransactions()` | `CopyFactoryTest::supports history endpoints` | Implemented and tested |
| `GET /users/current/subscription-transactions` | `History::subscriptionTransactions()` | `CopyFactoryTest::supports history endpoints` | Implemented and tested |
| `GET /users/current/strategies/{strategyId}/transactions/stream` | `History::strategyTransactionsStream()` | `CopyFactoryTest::supports history endpoints` | Implemented and tested |
| `GET /users/current/subscribers/{subscriberId}/transactions/stream` | `History::subscriberTransactionsStream()` | `CopyFactoryTest::supports history endpoints` | Implemented and tested |
| `GET /users/current/subscribers/{subscriberId}/signals` | `Trading::signals()` | `CopyFactoryTest::supports trading endpoints` | Implemented and tested at mocked request level |
| `GET /users/current/strategies/{strategyId}/external-signals` | `Trading::externalSignals()` | `CopyFactoryTest::supports trading endpoints` | Implemented and tested at mocked request level |
| `PUT /users/current/strategies/{strategyId}/external-signals/{signalId}` | `Trading::updateExternalSignal()` | `CopyFactoryTest::supports trading endpoints` | Implemented and tested at mocked request level |
| `POST /users/current/strategies/{strategyId}/external-signals/{signalId}/remove` | `Trading::removeExternalSignal()` | `CopyFactoryTest::supports trading endpoints` | Implemented and tested at mocked request level |
| `GET /users/current/subscribers/{subscriberId}/stopouts` | `Trading::stopouts()` | `CopyFactoryTest::supports trading endpoints` | Implemented and tested at mocked request level |
| `POST /users/current/subscribers/{subscriberId}/subscription-strategies/{strategyId}/stopouts/{reason}/reset` | `Trading::resetSubscriptionStopouts()` | `CopyFactoryTest::supports trading endpoints` | Implemented and tested at mocked request level |
| `POST /users/current/subscribers/{subscriberId}/stopouts/{reason}/reset` | `Trading::resetSubscriberStopouts()` | `CopyFactoryTest::supports trading endpoints` | Implemented and tested at mocked request level |
| `GET /users/current/stopouts/stream` | `Trading::stopoutsStream()` | `CopyFactoryTest::supports trading endpoints` | Implemented and tested at mocked request level |
| `POST /users/current/subscribers/{subscriberId}/resynchronize` | `Trading::resynchronize()` | `CopyFactoryTest::supports trading endpoints` | Implemented and tested at mocked request level |
| `GET /users/current/subscribers/{subscriberId}/user-log` | `Logs::userLog()` | `CopyFactoryTest::supports log endpoints` | Implemented and tested |
| `GET /users/current/subscribers/{subscriberId}/user-log/stream` | `Logs::userLogStream()` | `CopyFactoryTest::supports log endpoints` | Implemented and tested |
| `GET /users/current/strategies/{strategyId}/user-log` | `Logs::strategyLog()` | `CopyFactoryTest::supports log endpoints` | Implemented and tested |
| `GET /users/current/strategies/{strategyId}/user-log/stream` | `Logs::strategyLogStream()` | `CopyFactoryTest::supports log endpoints` | Implemented and tested |
| Copy trading configuration workflow | `CopyTrade::configureCopyTrading()` | `CopyFactoryTest` configuration workflow tests | Partial: workflow tested with mocks; real account role and API integration not verified |

## MetaStats

Official reference: [MetaStats API documentation](https://metaapi.cloud/docs/metastats/).

| Official HTTP endpoint | SDK class / method | Test evidence | Status / notes |
|---|---|---|---|
| `GET /users/current/accounts/{accountId}/metrics` | `Metrics::metrics()` | `MetaStatsTest::supports metastats metrics endpoints` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/historical-trades/{startTime}/{endTime}` | `Metrics::historicalTrades()` | `MetaStatsTest::supports metastats metrics endpoints` | Implemented and tested |
| `GET /users/current/accounts/{accountId}/open-trades` | `Metrics::openTrades()` | `MetaStatsTest::supports metastats metrics endpoints` | Implemented and tested |
| `DELETE /users/current/accounts/{accountId}` on MetaStats service | `Metrics::resetMetrics()` | `MetaStatsTest::supports metastats metrics endpoints` | Implemented and tested; distinct service URL from provisioning |

## MT Manager

Official reference: [MT Manager API documentation](https://metaapi.cloud/docs/manager/).

| Endpoint family | SDK class / method | Test evidence | Status / notes |
|---|---|---|---|
| `POST /users/current/mt{4,5}/provisioning-profiles/{profileId}/accounts` | `MetaTraderManager::createAccount()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `DELETE /users/current/mt{4,5}/provisioning-profiles/{profileId}/accounts/{login}` | `MetaTraderManager::removeAccount()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `PUT /users/current/mt{4,5}/provisioning-profiles/{profileId}/accounts/{login}/enabled` | `MetaTraderManager::setAccountEnabled()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `GET /users/current/mt{4,5}/provisioning-profiles/{profileId}/groups` | `MetaTraderManager::groups()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `GET /users/current/mt{4,5}/provisioning-profiles/{profileId}/groups/{groupName}/accounts` | `MetaTraderManager::accountsByGroup()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `GET /users/current/mt{4,5}/provisioning-profiles/{profileId}/accounts/{login}` | `MetaTraderManager::accountByLogin()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `GET /users/current/mt{4,5}/provisioning-profiles/{profileId}/groups/{groupName}` | `MetaTraderManager::groupByName()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `POST /users/current/mt{4,5}/provisioning-profiles/{profileId}/accounts/{login}/balance-transactions` | `MetaTraderManager::balanceOperation()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `GET /users/current/mt{4,5}/provisioning-profiles/{profileId}/accounts/{login}/account-information` | `MetaTraderManager::accountInformation()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `PUT /users/current/mt{4,5}/provisioning-profiles/{profileId}/accounts/{login}/change-password-permission` | `MetaTraderManager::setChangePasswordPermission()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `PUT /users/current/mt{4,5}/provisioning-profiles/{profileId}/accounts/{login}/group` | `MetaTraderManager::changeUserGroup()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `GET /users/current/mt{4,5}/provisioning-profiles/{profileId}/accounts/{login}/authentication-logs` | `MetaTraderManager::accountAuthenticationLogs()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `GET /users/current/mt{4,5}/provisioning-profiles/{profileId}/authentication-logs` | `MetaTraderManager::serverAuthenticationLogs()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `PUT /users/current/mt{4,5}/provisioning-profiles/{profileId}/accounts/{login}/readonly` | `MetaTraderManager::setAccountReadonly()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `POST /users/current/mt{4,5}/provisioning-profiles/{profileId}/accounts/{login}/checkPassword` | `MetaTraderManager::checkPassword()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `GET /users/current/mt{4,5}/provisioning-profiles/{profileId}/symbols` | `MetaTraderManager::symbols()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `GET /users/current/mt{4,5}/provisioning-profiles/{profileId}/symbolsGroup` | `MetaTraderManager::symbolsByGroup()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `PUT /users/current/mt{4,5}/provisioning-profiles/{profileId}/accounts/{login}/status` | `MetaTraderManager::setAccountStatus()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `PUT /users/current/mt4/provisioning-profiles/{profileId}/accounts/{login}/password` | `MetaTrader4Manager::changePassword()` | `ManagerApiTest::supports MT4 manager account and configuration endpoints` | Implemented and tested; route-level official confirmation pending |
| `PUT /users/current/mt5/provisioning-profiles/{profileId}/accounts/{login}/trading-enabled` | `MetaTrader5Manager::setAccountTrading()` | `ManagerApiTest::supports MT5 manager endpoints including trading permission` | Implemented and tested; route-level official confirmation pending |
| Every other current Manager Swagger operation | No matching inventory row yet | N/A | Partial: full official Manager operation list still needs to be enumerated |

## Risk Management

Official reference: [Risk Management API documentation](https://metaapi.cloud/docs/risk-management/).

| Official endpoint family | SDK class / method | Test evidence | Status / notes |
|---|---|---|---|
| `POST /users/current/accounts/{accountId}/trackers` | `Trackers::create()` | `RiskManagementApiTest::supports risk management tracker endpoints` | Implemented and tested; verify version behavior against current Swagger |
| `GET /users/current/accounts/{accountId}/trackers` | `Trackers::trackers()` | `RiskManagementApiTest::supports risk management tracker endpoints` | Implemented and tested; verify version behavior against current Swagger |
| `GET /users/current/accounts/{accountId}/trackers/{trackerId}` | `Trackers::tracker()` | `RiskManagementApiTest::supports risk management tracker endpoints` | Implemented and tested; verify version behavior against current Swagger |
| `GET /users/current/accounts/{accountId}/trackers/name/{name}` | `Trackers::trackerByName()` | `RiskManagementApiTest::supports risk management tracker endpoints` | Implemented and tested; verify version behavior against current Swagger |
| `PUT /users/current/accounts/{accountId}/trackers/{trackerId}` | `Trackers::update()` | `RiskManagementApiTest::supports risk management tracker endpoints` | Implemented and tested; verify version behavior against current Swagger |
| `DELETE /users/current/accounts/{accountId}/trackers/{trackerId}` | `Trackers::remove()` | `RiskManagementApiTest::supports risk management tracker endpoints` | Implemented and tested; verify version behavior against current Swagger |
| `GET /users/current/accounts/{accountId}/trackers/{trackerId}/statistics` | `Trackers::statistics()` | `RiskManagementApiTest::supports risk management tracker endpoints` | Implemented and tested; verify version behavior against current Swagger |
| `GET /users/current/accounts/{accountId}/equity-chart` | `Trackers::equityChart()` | `RiskManagementApiTest::supports risk management tracker endpoints` | Implemented and tested; verify version behavior against current Swagger |
| `GET /users/current/tracker-events/by-broker-time` | `Trackers::events()` | `RiskManagementApiTest::supports risk management tracker endpoints` | Implemented and tested; verify version behavior against current Swagger |
| `GET /users/current/tracker-events/stream` | `Trackers::eventStream()` | `RiskManagementApiTest::supports risk management tracker endpoints` | Implemented and tested; verify version behavior against current Swagger |
| API-version behavior for all Risk Management operations | Some methods expose version headers; others do not | Existing Risk Management tests | Partial: compare every method against current official version requirements |

## Not implemented / outside current REST coverage

| Capability | Current evidence | Status |
|---|---|---|
| Socket.IO/WebSocket streaming connection, terminal synchronization and real-time event subscriptions | Roadmap lists real-time streaming/WebSocket support as planned; no streaming client in `src/` | Not implemented |
| Independent streaming RPC connection manager | No RPC connection/client implementation found in `src/` | Not implemented |
| Full official API exhaustive inventory | No stable raw OpenAPI document was verified in this pass; the docs expose endpoint-level Swagger references | Partial / requires official Swagger endpoint inventory |
| Token-management and latency-tracking endpoint families | No matching resources found; exact current official endpoint list was not verified in this pass | Not classified as a confirmed API gap until official endpoints are enumerated |

## Reliability tests added in this change

| Concern | Test evidence | Expected behavior |
|---|---|---|
| Generated transaction ID visibility | `HttpReliabilityTest::exposes the generated transaction id for accepted account creation` | Caller can reuse the exact request ID when following up on a 202 response |
| Default HTTP timeout | `HttpReliabilityTest::configures safe timeout defaults for the SDK-owned client` | 10 second connect timeout and 60 second overall timeout unless overridden |
| Constructor timeout override | `HttpReliabilityTest::allows constructor timeout overrides for an SDK-owned client` | Explicit constructor values are applied |
| Injected Guzzle client defaults | `HttpReliabilityTest::preserves timeout configuration on an injected Guzzle client` | SDK preserves the injected client's configured defaults |
| Per-request timeout override | `HttpReliabilityTest::allows explicit per-request timeout overrides` | Explicit Guzzle options take precedence |
| Valid JSON scalar response | `HttpReliabilityTest::preserves valid JSON scalar responses as strings` | Public `array|string|null` return type is preserved |
| JSON scalar error body | `HttpReliabilityTest::normalizes valid JSON scalar error bodies without a type error` | Non-array JSON errors do not violate `MetaApiException` response types |
| JSON null response | `HttpReliabilityTest::preserves JSON null as response text rather than confusing it with an empty body` | Empty body remains `null`; JSON literal `null` remains the string `"null"` |
| Side-effecting POST retry safety | `HttpReliabilityTest::does not automatically retry a side-effecting request after a server error` | No automatic replay after 5xx or ambiguous transport failure |
| Terminal path encoding | `TerminalPathEncodingTest` state/history/market-data/trading/margin tests | Dynamic account IDs are encoded as one path segment across Terminal resources |

## Audit limitations and next steps

1. The current table maps the endpoint families visible in this SDK and links the corresponding official docs. It does not claim exhaustive coverage of all official endpoints.
2. The test status refers to direct Pest/mock tests, not live API integration.
3. Before publishing a coverage percentage, capture the complete current official Swagger/OpenAPI operation list, normalize every method/path, then compare it mechanically with this table and the SDK source.
4. Revisit this table when MetaApi changes endpoint versions or adds new methods.
