<?php

namespace Victorycodedev\MetaapiCloudPhpSdk\Resources\AccountManagement;

use Victorycodedev\MetaapiCloudPhpSdk\Http;
use Victorycodedev\MetaapiCloudPhpSdk\Resources\Path;
use Victorycodedev\MetaapiCloudPhpSdk\Responses\ActionResponse;

class AccountReplica
{
    public function __construct(private readonly Http $http) {}

    public function replicas(string $accountId): array|string|null
    {
        return $this->http->get('/users/current/accounts/' . Path::segment($accountId) . '/replicas');
    }

    public function replica(string $accountId, string $replicaId): array|string|null
    {
        return $this->http->get('/users/current/accounts/' . Path::segment($accountId) . '/replicas/' . Path::segment($replicaId));
    }

    public function createReplica(string $accountId, array $data, ?string $transactionId = null): ActionResponse
    {
        return $this->http->postAction(
            '/users/current/accounts/' . Path::segment($accountId) . '/replicas',
            $data,
            $this->transactionHeader($transactionId)
        );
    }

    private function transactionHeader(?string $transactionId): array
    {
        return ['transaction-id' => $transactionId ?? bin2hex(random_bytes(16))];
    }

    public function updateReplica(string $accountId, string $replicaId, array $data): array|string|null
    {
        return $this->http->put('/users/current/accounts/' . Path::segment($accountId) . '/replicas/' . Path::segment($replicaId), $data);
    }

    public function undeployReplica(string $accountId, string $replicaId): array|string|null
    {
        return $this->http->post('/users/current/accounts/' . Path::segment($accountId) . '/replicas/' . Path::segment($replicaId) . '/undeploy');
    }

    public function deployReplica(string $accountId, string $replicaId): array|string|null
    {
        return $this->http->post('/users/current/accounts/' . Path::segment($accountId) . '/replicas/' . Path::segment($replicaId) . '/deploy');
    }

    public function redeployReplica(string $accountId, string $replicaId): array|string|null
    {
        return $this->http->post('/users/current/accounts/' . Path::segment($accountId) . '/replicas/' . Path::segment($replicaId) . '/redeploy');
    }

    public function deleteReplica(string $accountId, string $replicaId): array|string|null
    {
        return $this->http->delete('/users/current/accounts/' . Path::segment($accountId) . '/replicas/' . Path::segment($replicaId));
    }

    public function generateReplicaCodeSample(string $accountId, string $replicaId, string $platform): array|string|null
    {
        return $this->http->get('/users/current/accounts/' . Path::segment($accountId) . '/replicas/' . Path::segment($replicaId) . '/examples/' . Path::segment($platform));
    }

    public function increaseReplicaReliability(string $accountId, string $replicaId): array|string|null
    {
        return $this->http->post('/users/current/accounts/' . Path::segment($accountId) . '/replicas/' . Path::segment($replicaId) . '/increase-reliability');
    }
}
