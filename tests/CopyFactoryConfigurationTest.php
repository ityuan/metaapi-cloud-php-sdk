<?php

use GuzzleHttp\Psr7\Response;

it('reads a strategy by id with path encoding', function (): void {
    $history = [];
    $copyFactory = copyFactoryWithHistory([new Response(200, [], '{"id":"strategy/id"}')], $history);

    expect($copyFactory->configuration()->strategy('strategy/id'))->toBe(['id' => 'strategy/id']);
    expect($history[0]['request'])->toHaveSentRequest(
        'GET',
        '/users/current/configuration/strategies/strategy%2Fid'
    );
});

it('updates a strategy and sends its payload', function (): void {
    $history = [];
    $copyFactory = copyFactoryWithHistory([new Response(204)], $history);

    $copyFactory->configuration()->updateStrategy('strategy/id', ['name' => 'Updated strategy']);

    expect($history[0]['request'])->toHaveSentRequest(
        'PUT',
        '/users/current/configuration/strategies/strategy%2Fid'
    );
    expect(json_decode((string) $history[0]['request']->getBody(), true))->toBe(['name' => 'Updated strategy']);
});

it('removes a strategy', function (): void {
    $history = [];
    $copyFactory = copyFactoryWithHistory([new Response(204)], $history);

    $copyFactory->configuration()->removeStrategy('strategy/id');

    expect($history[0]['request'])->toHaveSentRequest(
        'DELETE',
        '/users/current/configuration/strategies/strategy%2Fid'
    );
});

it('lists subscribers with filters', function (): void {
    $history = [];
    $copyFactory = copyFactoryWithHistory([new Response(200, [], '[]')], $history);

    $copyFactory->configuration()->subscribers(includeRemoved: true, limit: 25, offset: 5);

    expect($history[0]['request'])->toHaveSentRequest('GET', '/users/current/configuration/subscribers');
    expect($history[0]['request']->getUri()->getQuery())->toBe('includeRemoved=true&limit=25&offset=5');
});

it('lists subscribers using API version 2', function (): void {
    $history = [];
    $copyFactory = copyFactoryWithHistory([new Response(200, [], '[]')], $history);

    $copyFactory->configuration()->subscribersV2(limit: 20);

    expect($history[0]['request'])->toHaveSentRequest('GET', '/users/current/configuration/subscribers');
    expect($history[0]['request']->getHeaderLine('api-version'))->toBe('2');
    expect($history[0]['request']->getUri()->getQuery())->toContain('limit=20');
});

it('reads a subscriber by id with path encoding', function (): void {
    $history = [];
    $copyFactory = copyFactoryWithHistory([new Response(200, [], '{"id":"subscriber/id"}')], $history);

    expect($copyFactory->configuration()->subscriber('subscriber/id'))->toBe(['id' => 'subscriber/id']);
    expect($history[0]['request'])->toHaveSentRequest(
        'GET',
        '/users/current/configuration/subscribers/subscriber%2Fid'
    );
});

it('updates a subscriber and sends its payload', function (): void {
    $history = [];
    $copyFactory = copyFactoryWithHistory([new Response(204)], $history);

    $copyFactory->configuration()->updateSubscriber('subscriber/id', ['name' => 'Updated subscriber']);

    expect($history[0]['request'])->toHaveSentRequest(
        'PUT',
        '/users/current/configuration/subscribers/subscriber%2Fid'
    );
    expect(json_decode((string) $history[0]['request']->getBody(), true))->toBe(['name' => 'Updated subscriber']);
});

it('removes a subscriber', function (): void {
    $history = [];
    $copyFactory = copyFactoryWithHistory([new Response(204)], $history);

    $copyFactory->configuration()->removeSubscriber('subscriber/id');

    expect($history[0]['request'])->toHaveSentRequest(
        'DELETE',
        '/users/current/configuration/subscribers/subscriber%2Fid'
    );
});

it('deletes a subscription using encoded subscriber and strategy ids', function (): void {
    $history = [];
    $copyFactory = copyFactoryWithHistory([new Response(204)], $history);

    $copyFactory->configuration()->deleteSubscription('subscriber/id', 'strategy/id');

    expect($history[0]['request'])->toHaveSentRequest(
        'DELETE',
        '/users/current/configuration/subscribers/subscriber%2Fid/subscriptions/strategy%2Fid'
    );
});
