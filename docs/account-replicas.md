# Account Replicas

```php
$replicas = $metaapi->accountReplicas();
```

```php
$response = $replicas->createReplica('primary-account-id', [
    'magic' => 123456,
    'region' => 'london',
]);

if ($response->shouldRetry()) {
    echo "Replica creation accepted. Retry after: " . ($response->retryAfter() ?? 'not specified');
}
```

A transaction ID is auto-generated and returned by `$response->transactionId()`. If MetaApi responds with `202 Accepted`, keep the original payload and reuse the same transaction ID for the official follow-up request after `Retry-After`; do not replay the operation with a new ID. The SDK does not automatically retry requests.

Available methods:

- `replicas(string $accountId)`
- `replica(string $accountId, string $replicaId)`
- `createReplica(string $accountId, array $data, ?string $transactionId = null)` returns `ActionResponse`
- `updateReplica(string $accountId, string $replicaId, array $data)`
- `undeployReplica(string $accountId, string $replicaId)`
- `deployReplica(string $accountId, string $replicaId)`
- `redeployReplica(string $accountId, string $replicaId)`
- `deleteReplica(string $accountId, string $replicaId)`
- `generateReplicaCodeSample(string $accountId, string $replicaId, string $platform)`
- `increaseReplicaReliability(string $accountId, string $replicaId)`
