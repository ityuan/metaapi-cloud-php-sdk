<?php

namespace Victorycodedev\MetaapiCloudPhpSdk\Resources\Copyfactory;

use Victorycodedev\MetaapiCloudPhpSdk\Http;
use Victorycodedev\MetaapiCloudPhpSdk\Resources\Path;

class Webhooks
{
    private string $configUrl = 'users/current/configuration';

    public function __construct(private readonly Http $http)
    {
    }

    public function list(string $strategyId, array $query = []): array|string|null
    {
        return $this->http->get("/{$this->configUrl}/strategies/" . Path::segment($strategyId) . "/webhooks", $query);
    }

    public function create(string $strategyId, array $data): array|string|null
    {
        return $this->http->post("/{$this->configUrl}/strategies/" . Path::segment($strategyId) . "/webhooks", $data);
    }

    public function update(string $strategyId, string $webhookId, array $data): array|string|null
    {
        return $this->http->patch("/{$this->configUrl}/strategies/" . Path::segment($strategyId) . "/webhooks/" . Path::segment($webhookId), $data);
    }

    public function remove(string $strategyId, string $webhookId): array|string|null
    {
        return $this->http->delete("/{$this->configUrl}/strategies/" . Path::segment($strategyId) . "/webhooks/" . Path::segment($webhookId));
    }
}
