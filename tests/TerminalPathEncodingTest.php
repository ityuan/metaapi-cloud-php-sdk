<?php

use GuzzleHttp\Psr7\Response;

it('encodes account IDs in terminal state paths', function (): void {
    $history = [];
    $terminal = terminalWithHistory([new Response(200, [], '[]')], $history);

    $terminal->state()->positions('account/id');

    expect($history[0]['request'])->toHaveSentRequest(
        'GET',
        '/users/current/accounts/account%2Fid/positions'
    );
});

it('encodes account IDs in market data paths', function (): void {
    $history = [];
    $terminal = terminalWithHistory([new Response(200, [], '[]')], $history);

    $terminal->marketData()->symbols('account/id');

    expect($history[0]['request'])->toHaveSentRequest(
        'GET',
        '/users/current/accounts/account%2Fid/symbols'
    );
});

it('encodes account IDs in history paths', function (): void {
    $history = [];
    $terminal = terminalWithHistory([new Response(200, [], '[]')], $history);

    $terminal->history()->historyOrdersByTicket('account/id', 'ticket/id');

    expect($history[0]['request'])->toHaveSentRequest(
        'GET',
        '/users/current/accounts/account%2Fid/history-orders/ticket/ticket%2Fid'
    );
});

it('encodes account IDs in trading and margin paths', function (): void {
    $history = [];
    $terminal = terminalWithHistory([
        new Response(200, [], '{}'),
        new Response(200, [], '{}'),
    ], $history);

    $terminal->trading()->trade('account/id', ['actionType' => 'ORDER_TYPE_BUY']);
    $terminal->margin()->calculate('account/id', ['type' => 'ORDER_TYPE_BUY']);

    expect($history[0]['request'])->toHaveSentRequest(
        'POST',
        '/users/current/accounts/account%2Fid/trade'
    );
    expect($history[1]['request'])->toHaveSentRequest(
        'POST',
        '/users/current/accounts/account%2Fid/calculate-margin'
    );
});
