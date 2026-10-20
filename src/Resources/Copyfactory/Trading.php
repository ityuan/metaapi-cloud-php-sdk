<?php

namespace Victorycodedev\MetaapiCloudPhpSdk\Resources\Copyfactory;

use Victorycodedev\MetaapiCloudPhpSdk\Http;
use Victorycodedev\MetaapiCloudPhpSdk\Resources\Path;

class Trading
{
    public function __construct(private readonly Http $http)
    {
    }

    public function signals(string $subscriberId): array|string|null
    {
        return $this->http->get('/users/current/subscribers/' . Path::segment($subscriberId) . '/signals');
    }

    public function externalSignals(string $strategyId): array|string|null
    {
        return $this->http->get('/users/current/strategies/' . Path::segment($strategyId) . '/external-signals');
    }

    public function updateExternalSignal(string $strategyId, string $signalId, array $data): array|string|null
    {
        return $this->http->put('/users/current/strategies/' . Path::segment($strategyId) . '/external-signals/' . Path::segment($signalId), $data);
    }

    public function removeExternalSignal(string $strategyId, string $signalId, array $data): array|string|null
    {
        return $this->http->post('/users/current/strategies/' . Path::segment($strategyId) . '/external-signals/' . Path::segment($signalId) . '/remove', $data);
    }

    public function stopouts(string $subscriberId): array|string|null
    {
        return $this->http->get('/users/current/subscribers/' . Path::segment($subscriberId) . '/stopouts');
    }

    public function resetSubscriptionStopouts(string $subscriberId, string $strategyId, string $reason): array|string|null
    {
        return $this->http->post('/users/current/subscribers/' . Path::segment($subscriberId) . '/subscription-strategies/' . Path::segment($strategyId) . '/stopouts/' . Path::segment($reason) . '/reset');
    }

    public function resetSubscriberStopouts(string $subscriberId, string $reason): array|string|null
    {
        return $this->http->post('/users/current/subscribers/' . Path::segment($subscriberId) . '/stopouts/' . Path::segment($reason) . '/reset');
    }

    public function stopoutsStream(array $query = []): array|string|null
    {
        return $this->http->get('/users/current/stopouts/stream', $query);
    }

    public function resynchronize(string $subscriberId, array $query = []): array|string|null
    {
        return $this->http->post('/users/current/subscribers/' . Path::segment($subscriberId) . '/resynchronize', query: $query);
    }
}
