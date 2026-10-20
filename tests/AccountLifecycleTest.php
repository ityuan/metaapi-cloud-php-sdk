<?php

use GuzzleHttp\Psr7\Response;

it('reads an account by id and encodes the path segment', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(200, [], '{"id":"account/id"}')], $history);

    $result = $metaapi->accounts()->readById('account/id');

    expect($result)->toBe(['id' => 'account/id']);
    expect($history[0]['request'])->toHaveSentRequest('GET', '/users/current/accounts/account%2Fid');
});

it('updates an account and sends the request payload', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);

    $metaapi->accounts()->update('account/id', ['name' => 'Updated']);

    expect($history[0]['request'])->toHaveSentRequest('PUT', '/users/current/accounts/account%2Fid');
    expect(json_decode((string) $history[0]['request']->getBody(), true))->toBe(['name' => 'Updated']);
});

it('deploys an account with replica scope query', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);

    $metaapi->accounts()->deploy('account-id', executeForAllReplicas: false);

    expect($history[0]['request'])->toHaveSentRequest('POST', '/users/current/accounts/account-id/deploy');
    expect($history[0]['request']->getUri()->getQuery())->toBe('executeForAllReplicas=false');
});

it('undeploys an account with replica scope query', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);

    $metaapi->accounts()->undeploy('account-id', executeForAllReplicas: false);

    expect($history[0]['request'])->toHaveSentRequest('POST', '/users/current/accounts/account-id/undeploy');
    expect($history[0]['request']->getUri()->getQuery())->toBe('executeForAllReplicas=false');
});

it('redeploys an account with replica scope query', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);

    $metaapi->accounts()->redeploy('account-id', executeForAllReplicas: false);

    expect($history[0]['request'])->toHaveSentRequest('POST', '/users/current/accounts/account-id/redeploy');
    expect($history[0]['request']->getUri()->getQuery())->toBe('executeForAllReplicas=false');
});

it('creates a configuration link with ttl query', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(200, [], '{"configurationLink":"https://example.test/link"}')], $history);

    $result = $metaapi->accounts()->createConfigurationLink('account/id', ttlInDays: 3);

    expect($result)->toBe(['configurationLink' => 'https://example.test/link']);
    expect($history[0]['request'])->toHaveSentRequest('PUT', '/users/current/accounts/account%2Fid/configuration-link');
    expect($history[0]['request']->getUri()->getQuery())->toBe('ttlInDays=3');
});

it('enables CopyFactory API with role and slot payload', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);

    $metaapi->accounts()->enableCopyFactoryApi('account/id', ['PROVIDER', 'SUBSCRIBER'], 2);

    expect($history[0]['request'])->toHaveSentRequest('POST', '/users/current/accounts/account%2Fid/enable-copy-factory-api');
    expect(json_decode((string) $history[0]['request']->getBody(), true))->toBe([
        'copyFactoryRoles' => ['PROVIDER', 'SUBSCRIBER'],
        'copyFactoryResourceSlots' => 2,
    ]);
});
