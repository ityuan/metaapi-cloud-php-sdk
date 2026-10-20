<?php

namespace Victorycodedev\MetaapiCloudPhpSdk\Resources\Copyfactory;

use Victorycodedev\MetaapiCloudPhpSdk\Http;
use Victorycodedev\MetaapiCloudPhpSdk\Resources\Path;

class Configuration
{
    private string $configUrl = 'users/current/configuration';

    public function __construct(private readonly Http $http)
    {
    }

    /*
    *   Generates a new strategy id
    */
    public function generateStrategyId(): array|string|null
    {
        return $this->http->get("/{$this->configUrl}/unused-strategy-id");
    }

    /*
    *   Returns provider strategies the user has configured
    */
    public function strategies(bool $includeRemoved = false, int $limit = 1000, int $offset = 0): array|string|null
    {
        return $this->http->get("/{$this->configUrl}/strategies", [
            'includeRemoved' => $includeRemoved,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    public function strategiesV2(bool $includeRemoved = false, int $limit = 1000, int $offset = 0): array|string|null
    {
        return $this->http->get(
            "/{$this->configUrl}/strategies",
            [
                'includeRemoved' => $includeRemoved,
                'limit' => $limit,
                'offset' => $offset,
            ],
            ['api-version' => '2']
        );
    }

    /*
    *   Returns provider strategy the user has configured by id
    */
    public function strategy(string $strategyId): array|string|null
    {
        return $this->http->get("/{$this->configUrl}/strategies/" . Path::segment($strategyId));
    }

    /*
    *   Updates provider strategy
    */
    public function updateStrategy(string $strategyId, array $data): array|string|null
    {
        return $this->http->put("/{$this->configUrl}/strategies/" . Path::segment($strategyId), $data);
    }

    /*
    *   Deletes provider strategy
    */
    public function removeStrategy(string $strategyId): array|string|null
    {
        return $this->http->delete("/{$this->configUrl}/strategies/" . Path::segment($strategyId));
    }

    public function portfolioStrategies(bool $includeRemoved = false, int $limit = 1000, int $offset = 0, ?int $apiVersion = null): array|string|null
    {
        return $this->http->get(
            "/{$this->configUrl}/portfolio-strategies",
            [
                'includeRemoved' => $includeRemoved,
                'limit' => $limit,
                'offset' => $offset,
            ],
            $apiVersion ? ['api-version' => (string) $apiVersion] : []
        );
    }

    public function portfolioStrategy(string $portfolioId): array|string|null
    {
        return $this->http->get("/{$this->configUrl}/portfolio-strategies/" . Path::segment($portfolioId));
    }

    public function updatePortfolioStrategy(string $portfolioId, array $data): array|string|null
    {
        return $this->http->put("/{$this->configUrl}/portfolio-strategies/" . Path::segment($portfolioId), $data);
    }

    public function removePortfolioStrategy(string $portfolioId, array $closeInstructions = []): array|string|null
    {
        return $this->http->delete("/{$this->configUrl}/portfolio-strategies/" . Path::segment($portfolioId), payload: $closeInstructions);
    }

    public function removePortfolioStrategyMember(string $portfolioId, string $strategyId, array $closeInstructions = []): array|string|null
    {
        return $this->http->delete(
            "/{$this->configUrl}/portfolio-strategies/" . Path::segment($portfolioId) . '/members/' . Path::segment($strategyId),
            payload: $closeInstructions
        );
    }

    /*
    *   Returns strategy subscribers the current user provides strategies to
    */
    public function subscribers(bool $includeRemoved = false, int $limit = 1000, int $offset = 0): array|string|null
    {
        return $this->http->get("/{$this->configUrl}/subscribers", [
            'includeRemoved' => $includeRemoved,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    public function subscribersV2(bool $includeRemoved = false, int $limit = 1000, int $offset = 0): array|string|null
    {
        return $this->http->get(
            "/{$this->configUrl}/subscribers",
            [
                'includeRemoved' => $includeRemoved,
                'limit' => $limit,
                'offset' => $offset,
            ],
            ['api-version' => '2']
        );
    }

    /*
    *   Returns CopyFactory subscriber by id
    */
    public function subscriber(string $subscriberId): array|string|null
    {
        return $this->http->get("/{$this->configUrl}/subscribers/" . Path::segment($subscriberId));
    }

    /*
    *   Updates subscriber configuration
    */
    public function updateSubscriber(string $subscriberId, array $data): array|string|null
    {
        return $this->http->put("/{$this->configUrl}/subscribers/" . Path::segment($subscriberId), $data);
    }

    /*
    *   Deletes subscriber configuration
    */
    public function removeSubscriber(string $subscriberId): array|string|null
    {
        return $this->http->delete("/{$this->configUrl}/subscribers/" . Path::segment($subscriberId));
    }

    /*
    *   Deletes subscription
    */
    public function deleteSubscription(string $subscriberId, string $strategyId): array|string|null
    {
        return $this->http->delete(
            "/{$this->configUrl}/subscribers/" . Path::segment($subscriberId) . '/subscriptions/' . Path::segment($strategyId)
        );
    }
}
