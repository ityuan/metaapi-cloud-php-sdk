<?php

use GuzzleHttp\Psr7\Response;

it('reads a provisioning profile by id with path encoding', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(200, [], '{"id":"profile/id","version":5}')], $history);

    expect($metaapi->provisioningProfiles()->provisioningProfile('profile/id'))->toBe([
        'id' => 'profile/id',
        'version' => 5,
    ]);
    expect($history[0]['request'])->toHaveSentRequest(
        'GET',
        '/users/current/provisioning-profiles/profile%2Fid'
    );
});

it('updates a provisioning profile', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);

    $metaapi->provisioningProfiles()->updateProvisioningProfile('profile/id', [
        'name' => 'Updated profile',
        'brokerTimezone' => 'EET',
    ]);

    expect($history[0]['request'])->toHaveSentRequest(
        'PUT',
        '/users/current/provisioning-profiles/profile%2Fid'
    );
    expect(json_decode((string) $history[0]['request']->getBody(), true))->toBe([
        'name' => 'Updated profile',
        'brokerTimezone' => 'EET',
    ]);
});

it('uploads a provisioning profile file using encoded path segments and multipart content', function (): void {
    $history = [];
    $metaapi = metaApiClientWithHistory([new Response(204)], $history);
    $file = tempnam(sys_get_temp_dir(), 'metaapi-profile-test');
    file_put_contents($file, 'server-data');

    try {
        $metaapi->provisioningProfiles()->uploadProvisioningProfileFile(
            'profile/id',
            'servers config.dat',
            $file
        );
    } finally {
        unlink($file);
    }

    $request = $history[0]['request'];

    expect($request)->toHaveSentRequest(
        'PUT',
        '/users/current/provisioning-profiles/profile%2Fid/servers%20config.dat'
    );
    expect($request->getHeaderLine('Content-Type'))->toContain('multipart/form-data');
});
