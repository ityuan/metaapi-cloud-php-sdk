<?php

namespace Victorycodedev\MetaapiCloudPhpSdk\Responses;

class ActionResponse
{
    public function __construct(
        private readonly array|string|null $body,
        private readonly int $statusCode,
        private readonly array $headers = [],
        private readonly ?string $transactionId = null
    ) {}

    public function body(): array|string|null
    {
        return $this->body;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Returns the transaction-id used for this request, when supplied by the SDK.
     *
     * Reuse this identifier when following up on an accepted operation. Do not
     * replay a side-effecting POST merely because this response is accepted.
     */
    public function transactionId(): ?string
    {
        return $this->transactionId;
    }

    public function retryAfter(): ?string
    {
        return $this->header('Retry-After') ?? $this->header('retry-after');
    }

    public function isCreated(): bool
    {
        return $this->statusCode === 201;
    }

    public function isAccepted(): bool
    {
        return $this->statusCode === 202;
    }

    /**
     * Indicates that follow-up handling may be required; it does not mean that
     * an operation with side effects is safe to submit again.
     */
    public function shouldRetry(): bool
    {
        return $this->isAccepted() || $this->retryAfter() !== null;
    }

    public function id(): ?string
    {
        return $this->field('id');
    }

    public function state(): ?string
    {
        return $this->field('state');
    }

    private function header(string $name): ?string
    {
        $values = $this->headers[$name] ?? null;

        return is_array($values) && $values !== [] ? (string) $values[0] : null;
    }

    private function field(string $name): ?string
    {
        return is_array($this->body) && isset($this->body[$name])
            ? (string) $this->body[$name]
            : null;
    }
}
