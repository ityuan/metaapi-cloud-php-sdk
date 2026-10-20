<?php

use GuzzleHttp\Psr7\Response;
use Victorycodedev\MetaapiCloudPhpSdk\Responses\ActionResponse;

it('reads accounts with filters and api version header', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(200, [], '{"count":0,"items":[]}')], $history);

    $metaapi->accounts()->accounts([
        'deploymentStatus' => ['deployed'],
        'limit'            => 50,
    ], apiVersion: 2);

    $request = $history[0]['request'];

    expect($request)->toHaveSentRequest('GET', '/users/current/accounts');
    expect($request->getHeaderLine('api-version'))->toBe('2');
    expect($request->getUri()->getQuery())->toContain('deploymentStatus%5B0%5D=deployed');
    expect($request->getUri()->getQuery())->toContain('limit=50');
});

it('creates accounts with transaction id headers', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(201, [], '{"id":"account-id","state":"DEPLOYED"}')], $history);

    $response = $metaapi->accounts()->create(['name' => 'Demo', 'server' => 'ICMarketsSC-Demo'], '12345678901234567890123456789012');

    expect($response)->toBeInstanceOf(ActionResponse::class);
    expect($response->body())->toBe(['id' => 'account-id', 'state' => 'DEPLOYED']);
    expect($response->statusCode())->toBe(201);
    expect($response->isCreated())->toBeTrue();
    expect($response->isAccepted())->toBeFalse();
    expect($response->shouldRetry())->toBeFalse();
    expect($response->id())->toBe('account-id');
    expect($response->state())->toBe('DEPLOYED');
    expect($history[0]['request'])->toHaveSentRequest('POST', '/users/current/accounts');
    expect($history[0]['request']->getHeaderLine('transaction-id'))->toBe('12345678901234567890123456789012');
});

it('auto-generates transaction id for account create when not provided', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(201, [], '{}')], $history);

    $metaapi->accounts()->create(['name' => 'Demo']);

    $transactionId = $history[0]['request']->getHeaderLine('transaction-id');
    expect($transactionId)->not->toBeEmpty();
    expect(strlen($transactionId))->toBe(32);
    expect(ctype_xdigit($transactionId))->toBeTrue();
});

it('exposes accepted account creation retry metadata', function (): void {
    $metaapi = metaApiClientWithHistory([
        new Response(202, ['Retry-After' => '10'], '{"id":"account-id","state":"DRAFT"}'),
    ]);

    $response = $metaapi->accounts()->create(['name' => 'Demo']);

    expect($response->isAccepted())->toBeTrue();
    expect($response->shouldRetry())->toBeTrue();
    expect($response->retryAfter())->toBe('10');
    expect($response->id())->toBe('account-id');
    expect($response->state())->toBe('DRAFT');
});

it('deletes accounts using the account endpoint', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);

    expect($metaapi->accounts()->delete('account-id', true))->toBeNull();
    expect($history[0]['request'])->toHaveSentRequest('DELETE', '/users/current/accounts/account-id');
    expect($history[0]['request']->getUri()->getQuery())->toBe('executeForAllReplicas=true');
});

it('enables account features', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);

    $metaapi->accounts()->enableFeatures('account-id', [
        'metastatsApiEnabled' => true,
        'reliabilityIncreased' => true,
    ]);

    expect($history[0]['request'])->toHaveSentRequest('POST', '/users/current/accounts/account-id/enable-account-features');
});

it('creates account replicas', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(201, [], '{"id":"replica-id","state":"DEPLOYED"}')], $history);

    $response = $metaapi->accountReplicas()->createReplica('account-id', ['magic' => 123456, 'region' => 'london'], 'transaction-id');

    expect($response)->toBeInstanceOf(ActionResponse::class);
    expect($response->isCreated())->toBeTrue();
    expect($response->id())->toBe('replica-id');
    expect($history[0]['request'])->toHaveSentRequest('POST', '/users/current/accounts/account-id/replicas');
    expect($history[0]['request']->getHeaderLine('transaction-id'))->toBe('transaction-id');
});

it('auto-generates transaction id for replica create when not provided', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(201, [], '{}')], $history);

    $metaapi->accountReplicas()->createReplica('account-id', ['magic' => 123456]);

    $transactionId = $history[0]['request']->getHeaderLine('transaction-id');
    expect($transactionId)->not->toBeEmpty();
    expect(strlen($transactionId))->toBe(32);
    expect(ctype_xdigit($transactionId))->toBeTrue();
});

it('exposes accepted account replica creation retry metadata', function (): void {
    $metaapi = metaApiClientWithHistory([
        new Response(202, ['Retry-After' => '15'], '{"id":"replica-id","state":"DRAFT"}'),
    ]);

    $response = $metaapi->accountReplicas()->createReplica('account-id', ['magic' => 123456]);

    expect($response->isAccepted())->toBeTrue();
    expect($response->shouldRetry())->toBeTrue();
    expect($response->retryAfter())->toBe('15');
    expect($response->id())->toBe('replica-id');
    expect($response->state())->toBe('DRAFT');
});


it('returns the generated account creation transaction id without breaking the request header', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(202, ['Retry-After' => '5'], '{"id":"account-id","state":"DRAFT"}')], $history);

    $response = $metaapi->accounts()->create(['name' => 'Demo']);

    expect($response->transactionId())->toBe($history[0]['request']->getHeaderLine('transaction-id'));
    expect(strlen((string) $response->transactionId()))->toBe(32);
    expect($response->isAccepted())->toBeTrue();
});

it('encodes account path segments and tests lifecycle and configuration-link endpoints', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory(array_fill(0, 7, new Response(204)), $history);

    $metaapi->accounts()->readById('account/id');
    $metaapi->accounts()->update('account/id', ['name' => 'Updated']);
    $metaapi->accounts()->deploy('account/id', false);
    $metaapi->accounts()->undeploy('account/id', false);
    $metaapi->accounts()->redeploy('account/id', false);
    $metaapi->accounts()->generateCodeSample('account/id', 'MQL/5');
    $metaapi->accounts()->createConfigurationLink('account/id', 7);

    expect($history[0]['request'])->toHaveSentRequest('GET', '/users/current/accounts/account%2Fid');
    expect($history[1]['request'])->toHaveSentRequest('PUT', '/users/current/accounts/account%2Fid');
    expect($history[1]['request']->getBody()->getContents())->toContain('"name":"Updated"');
    expect($history[2]['request'])->toHaveSentRequest('POST', '/users/current/accounts/account%2Fid/deploy');
    expect($history[2]['request']->getUri()->getQuery())->toBe('executeForAllReplicas=false');
    expect($history[3]['request'])->toHaveSentRequest('POST', '/users/current/accounts/account%2Fid/undeploy');
    expect($history[4]['request'])->toHaveSentRequest('POST', '/users/current/accounts/account%2Fid/redeploy');
    expect($history[5]['request'])->toHaveSentRequest('GET', '/users/current/accounts/account%2Fid/examples/MQL%2F5');
    expect($history[6]['request'])->toHaveSentRequest('PUT', '/users/current/accounts/account%2Fid/configuration-link');
    expect($history[6]['request']->getUri()->getQuery())->toBe('ttlInDays=7');
});

it('tests account replica lifecycle endpoints and encodes path segments', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory(array_fill(0, 8, new Response(204)), $history);

    $metaapi->accountReplicas()->replicas('account/id');
    $metaapi->accountReplicas()->replica('account/id', 'replica/id');
    $metaapi->accountReplicas()->updateReplica('account/id', 'replica/id', ['region' => 'london']);
    $metaapi->accountReplicas()->deployReplica('account/id', 'replica/id');
    $metaapi->accountReplicas()->undeployReplica('account/id', 'replica/id');
    $metaapi->accountReplicas()->redeployReplica('account/id', 'replica/id');
    $metaapi->accountReplicas()->deleteReplica('account/id', 'replica/id');
    $metaapi->accountReplicas()->increaseReplicaReliability('account/id', 'replica/id');

    expect($history[0]['request'])->toHaveSentRequest('GET', '/users/current/accounts/account%2Fid/replicas');
    expect($history[1]['request'])->toHaveSentRequest('GET', '/users/current/accounts/account%2Fid/replicas/replica%2Fid');
    expect($history[2]['request'])->toHaveSentRequest('PUT', '/users/current/accounts/account%2Fid/replicas/replica%2Fid');
    expect($history[3]['request'])->toHaveSentRequest('POST', '/users/current/accounts/account%2Fid/replicas/replica%2Fid/deploy');
    expect($history[4]['request'])->toHaveSentRequest('POST', '/users/current/accounts/account%2Fid/replicas/replica%2Fid/undeploy');
    expect($history[5]['request'])->toHaveSentRequest('POST', '/users/current/accounts/account%2Fid/replicas/replica%2Fid/redeploy');
    expect($history[6]['request'])->toHaveSentRequest('DELETE', '/users/current/accounts/account%2Fid/replicas/replica%2Fid');
    expect($history[7]['request'])->toHaveSentRequest('POST', '/users/current/accounts/account%2Fid/replicas/replica%2Fid/increase-reliability');
});
