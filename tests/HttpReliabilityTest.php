<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Victorycodedev\MetaapiCloudPhpSdk\Exceptions\MetaApiException;
use Victorycodedev\MetaapiCloudPhpSdk\Http;

it('exposes the generated transaction id for accepted account creation', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([
        new Response(202, ['Retry-After' => '10'], '{"id":"account-id","state":"DRAFT"}'),
    ], $history);

    $response = $metaapi->accounts()->create(['name' => 'Demo']);

    $requestTransactionId = $history[0]['request']->getHeaderLine('transaction-id');

    expect($response->transactionId())->toBe($requestTransactionId);
    expect($response->transactionId())->toMatch('/^[a-f0-9]{32}$/');
    expect($response->isAccepted())->toBeTrue();
    expect($response->retryAfter())->toBe('10');
});

it('configures safe timeout defaults for the SDK-owned client', function (): void {
    $http = new Http('test-token', 'https://example.test');
    $clientProperty = new ReflectionProperty(Http::class, 'client');
    $client = $clientProperty->getValue($http);

    expect($client->getConfig('connect_timeout'))->toBe(10.0);
    expect($client->getConfig('timeout'))->toBe(60.0);
});


it('allows constructor timeout overrides for an SDK-owned client', function (): void {
    $http = new Http('test-token', 'https://example.test', timeout: 120.0, connectTimeout: 5.0);
    $clientProperty = new ReflectionProperty(Http::class, 'client');
    $client = $clientProperty->getValue($http);

    expect($client->getConfig('connect_timeout'))->toBe(5.0);
    expect($client->getConfig('timeout'))->toBe(120.0);
});

it('preserves timeout configuration on an injected Guzzle client', function (): void {
    $history = [];
    $stack = HandlerStack::create(new MockHandler([new Response(200, [], '{}')]));
    $stack->push(Middleware::history($history));
    $client = new Client([
        'handler' => $stack,
        'timeout' => 30.0,
        'connect_timeout' => 4.0,
    ]);
    $http = new Http('test-token', 'https://example.test', $client);

    $http->get('/users/current/accounts');

    expect($history[0]['options']['connect_timeout'])->toBe(4.0);
    expect($history[0]['options']['timeout'])->toBe(30.0);
});

it('allows explicit per-request timeout overrides', function (): void {
    $history = [];
    $http = mockHttp([new Response(200, [], '{}')], $history);

    $http->request('GET', '/users/current/accounts', [
        'timeout' => 12.0,
        'connect_timeout' => 2.0,
    ]);

    expect($history[0]['options']['connect_timeout'])->toBe(2.0);
    expect($history[0]['options']['timeout'])->toBe(12.0);
});

it('preserves valid JSON scalar responses as strings', function (): void {
    $http = mockHttp([
        new Response(200, [], '42'),
    ]);

    expect($http->get('/test/scalar'))->toBe('42');
});

it('preserves JSON null as response text rather than confusing it with an empty body', function (): void {
    $http = mockHttp([
        new Response(200, [], 'null'),
    ]);

    expect($http->get('/test/null'))->toBe('null');
});

it('does not automatically retry a side-effecting request after a server error', function (): void {
    $history = [];
    $http = mockHttp([
        new Response(503, [], '{"message":"Temporary server error"}'),
        new Response(201, [], '{"id":"must-not-be-used"}'),
    ], $history);

    expect(fn () => $http->post('/users/current/accounts', ['name' => 'Demo']))
        ->toThrow(MetaApiException::class, 'Temporary server error');

    expect($history)->toHaveCount(1);
});

it('normalizes valid JSON scalar error bodies without a type error', function (): void {
    $http = mockHttp([
        new Response(502, [], '42'),
    ]);

    expect(fn () => $http->get('/test/error-scalar'))
        ->toThrow(MetaApiException::class, '42');
});
