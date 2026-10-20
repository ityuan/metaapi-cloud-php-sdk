<?php

use GuzzleHttp\Psr7\Response;

it('lists account replicas', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(200, [], '[]')], $history);

    expect($metaapi->accountReplicas()->replicas('account/id'))->toBe([]);
    expect($history[0]['request'])->toHaveSentRequest('GET', '/users/current/accounts/account%2Fid/replicas');
});

it('reads a replica by id with encoded path segments', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(200, [], '{"id":"replica/id"}')], $history);

    expect($metaapi->accountReplicas()->replica('account/id', 'replica/id'))->toBe(['id' => 'replica/id']);
    expect($history[0]['request'])->toHaveSentRequest('GET', '/users/current/accounts/account%2Fid/replicas/replica%2Fid');
});

it('updates an account replica', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);

    $metaapi->accountReplicas()->updateReplica('account-id', 'replica-id', ['region' => 'london']);

    expect($history[0]['request'])->toHaveSentRequest('PUT', '/users/current/accounts/account-id/replicas/replica-id');
    expect(json_decode((string) $history[0]['request']->getBody(), true))->toBe(['region' => 'london']);
});

it('deploys an account replica', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);

    $metaapi->accountReplicas()->deployReplica('account-id', 'replica-id');

    expect($history[0]['request'])->toHaveSentRequest('POST', '/users/current/accounts/account-id/replicas/replica-id/deploy');
});

it('undeploys an account replica', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);

    $metaapi->accountReplicas()->undeployReplica('account-id', 'replica-id');

    expect($history[0]['request'])->toHaveSentRequest('POST', '/users/current/accounts/account-id/replicas/replica-id/undeploy');
});

it('redeploys an account replica', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);

    $metaapi->accountReplicas()->redeployReplica('account-id', 'replica-id');

    expect($history[0]['request'])->toHaveSentRequest('POST', '/users/current/accounts/account-id/replicas/replica-id/redeploy');
});

it('deletes an account replica', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);

    expect($metaapi->accountReplicas()->deleteReplica('account-id', 'replica-id'))->toBeNull();
    expect($history[0]['request'])->toHaveSentRequest('DELETE', '/users/current/accounts/account-id/replicas/replica-id');
});

it('generates a replica code sample with encoded platform segment', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(200, [], '{"code":"sample"}')], $history);

    $metaapi->accountReplicas()->generateReplicaCodeSample('account-id', 'replica-id', 'mt/5');

    expect($history[0]['request'])->toHaveSentRequest(
        'GET',
        '/users/current/accounts/account-id/replicas/replica-id/examples/mt%2F5'
    );
});

it('increases account replica reliability', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);

    $metaapi->accountReplicas()->increaseReplicaReliability('account-id', 'replica-id');

    expect($history[0]['request'])->toHaveSentRequest(
        'POST',
        '/users/current/accounts/account-id/replicas/replica-id/increase-reliability'
    );
});
