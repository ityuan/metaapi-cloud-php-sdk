<?php

use GuzzleHttp\Psr7\Response;

it('reads provisioning profiles with filters', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(200, [], '[]')], $history);

    $metaapi->provisioningProfiles()->provisioningProfiles([
        'version' => 5,
        'status'  => 'new',
        'type'    => 'mtTerminal',
    ], apiVersion: 2);

    $request = $history[0]['request'];

    expect($request)->toHaveSentRequest('GET', '/users/current/provisioning-profiles');
    expect($request->getHeaderLine('api-version'))->toBe('2');
    expect($request->getUri()->getQuery())->toContain('version=5');
    expect($request->getUri()->getQuery())->toContain('status=new');
});

it('creates provisioning profiles', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(201, [], '{"id":"profile-id"}')], $history);

    $response = $metaapi->provisioningProfiles()->createProvisioningProfile([
        'name'                     => 'ICMarkets',
        'version'                  => 5,
        'brokerTimezone'           => 'EET',
        'brokerDSTSwitchTimezone'  => 'EET',
    ]);

    expect($response)->toBe(['id' => 'profile-id']);
    expect($history[0]['request'])->toHaveSentRequest('POST', '/users/current/provisioning-profiles');
});

it('deletes provisioning profiles', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);

    expect($metaapi->provisioningProfiles()->deleteProvisioningProfile('profile-id'))->toBeNull();
    expect($history[0]['request'])->toHaveSentRequest('DELETE', '/users/current/provisioning-profiles/profile-id');
});


it('reads and updates a provisioning profile with encoded path parameters', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([
        new Response(200, [], '{"_id":"profile/id","name":"Example"}'),
        new Response(204),
    ], $history);

    expect($metaapi->provisioningProfiles()->provisioningProfile('profile/id'))->toBe(['_id' => 'profile/id', 'name' => 'Example']);
    $metaapi->provisioningProfiles()->updateProvisioningProfile('profile/id', ['name' => 'Updated']);

    expect($history[0]['request'])->toHaveSentRequest('GET', '/users/current/provisioning-profiles/profile%2Fid');
    expect($history[1]['request'])->toHaveSentRequest('PUT', '/users/current/provisioning-profiles/profile%2Fid');
    expect($history[1]['request']->getBody()->getContents())->toContain('"name":"Updated"');
});

it('uploads provisioning profile files with encoded path segments', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);
    $filePath = tempnam(sys_get_temp_dir(), 'metaapi-profile');
    file_put_contents($filePath, 'server data');

    try {
        $metaapi->provisioningProfiles()->uploadProvisioningProfileFile('profile/id', 'servers config.dat', $filePath);
    } finally {
        unlink($filePath);
    }

    expect($history[0]['request'])->toHaveSentRequest('PUT', '/users/current/provisioning-profiles/profile%2Fid/servers%20config.dat');
    expect($history[0]['request']->getHeaderLine('Content-Type'))->toContain('multipart/form-data');
});
