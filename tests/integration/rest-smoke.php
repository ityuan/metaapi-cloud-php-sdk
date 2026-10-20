<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Victorycodedev\MetaapiCloudPhpSdk\Exceptions\MetaApiException;
use Victorycodedev\MetaapiCloudPhpSdk\MetaApiClient;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

$token = getenv('METAAPI_AUTH_TOKEN') ?: '';
$accountId = getenv('METAAPI_ACCOUNT_ID') ?: '';
$region = strtolower(trim(getenv('METAAPI_REGION') ?: 'new-york'));
$serverUrl = getenv('METAAPI_TERMINAL_URL') ?: null;

if ($token === '' || $accountId === '') {
    fwrite(
        STDERR,
        "Missing METAAPI_AUTH_TOKEN or METAAPI_ACCOUNT_ID. Set these in your local environment; do not commit them.\n"
    );
    exit(2);
}

if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $region)) {
    fwrite(STDERR, "METAAPI_REGION must be a lowercase region name such as new-york or london.\n");
    exit(2);
}

$history = [];
$handler = HandlerStack::create();
$handler->push(Middleware::history($history));
$client = new Client([
    'handler' => $handler,
    'timeout' => 30.0,
    'connect_timeout' => 10.0,
    'http_errors' => false,
]);

$api = new MetaApiClient($token, $client);

try {
    // Read-only call: this script never creates, modifies, deploys, or trades an account.
    $information = $api->terminal(region: $region, serverUrl: $serverUrl)
        ->state()
        ->accountInformation($accountId);
} catch (MetaApiException $exception) {
    fwrite(
        STDERR,
        sprintf(
            "FAIL: real REST GET failed (HTTP %d, MetaApi error id %d, error %s). No token or response body was printed.\n",
            $exception->statusCode(),
            $exception->id(),
            $exception->error() ?? 'unknown'
        )
    );
    exit(1);
}

$entry = $history[0] ?? null;
if (!is_array($entry) || !isset($entry['request'], $entry['response'])) {
    fwrite(STDERR, "FAIL: HTTP middleware did not capture the real request and response.\n");
    exit(1);
}

$request = $entry['request'];
$response = $entry['response'];
$expectedHost = $serverUrl !== null
    ? parse_url($serverUrl, PHP_URL_HOST)
    : "mt-client-api-v1.{$region}.agiliumtrade.ai";
$expectedPath = '/users/current/accounts/' . rawurlencode($accountId) . '/account-information';

if ($request->getMethod() !== 'GET') {
    fwrite(STDERR, "FAIL: SDK sent an unexpected HTTP method.\n");
    exit(1);
}

if ($expectedHost === false || $request->getUri()->getHost() !== $expectedHost) {
    fwrite(STDERR, "FAIL: SDK sent the request to an unexpected regional host.\n");
    exit(1);
}

if ($request->getUri()->getPath() !== $expectedPath) {
    fwrite(STDERR, "FAIL: SDK sent an unexpected account-information path.\n");
    exit(1);
}

if ($request->getHeaderLine('auth-token') === '') {
    fwrite(STDERR, "FAIL: auth-token header was missing. The token value was not printed.\n");
    exit(1);
}

if ($request->getHeaderLine('Accept') !== 'application/json') {
    fwrite(STDERR, "FAIL: Accept header was not application/json.\n");
    exit(1);
}

if ($request->getUri()->getQuery() !== 'refreshTerminalState=false') {
    fwrite(STDERR, "FAIL: refreshTerminalState query parameter did not match the SDK contract.\n");
    exit(1);
}

if ($response->getStatusCode() !== 200) {
    fwrite(STDERR, sprintf("FAIL: expected HTTP 200, got HTTP %d.\n", $response->getStatusCode()));
    exit(1);
}

if (!is_array($information)) {
    fwrite(STDERR, "FAIL: account-information response was not decoded as a JSON object.\n");
    exit(1);
}

foreach (['balance', 'equity'] as $field) {
    if (!array_key_exists($field, $information) || !is_numeric($information[$field])) {
        fwrite(STDERR, sprintf("FAIL: response field %s was missing or non-numeric.\n", $field));
        exit(1);
    }
}

foreach (['currency', 'server'] as $field) {
    if (!isset($information[$field]) || !is_string($information[$field])) {
        fwrite(STDERR, sprintf("FAIL: response field %s was missing or not a string.\n", $field));
        exit(1);
    }
}

fwrite(
    STDOUT,
    sprintf(
        "PASS: live REST GET account-information returned HTTP 200; host=%s; auth-token present; path/query and response schema validated. Response keys: %s\n",
        $request->getUri()->getHost(),
        implode(', ', array_keys($information))
    )
);

if (in_array('--check-404', $argv, true)) {
    $hex = bin2hex(random_bytes(16));
    $unknownAccountId = substr($hex, 0, 8) . '-' . substr($hex, 8, 4) . '-4' .
        substr($hex, 13, 3) . '-a' . substr($hex, 17, 3) . '-' . substr($hex, 20, 12);

    try {
        $api->terminal(region: $region, serverUrl: $serverUrl)
            ->state()
            ->accountInformation($unknownAccountId);
        fwrite(STDERR, "FAIL: random, read-only account lookup unexpectedly succeeded; 404 contract was not checked.\n");
        exit(1);
    } catch (MetaApiException $exception) {
        if ($exception->statusCode() !== 404) {
            fwrite(
                STDERR,
                sprintf(
                    "FAIL: expected documented HTTP 404 for an unknown account, got HTTP %d (error id %d). No response body was printed.\n",
                    $exception->statusCode(),
                    $exception->id()
                )
            );
            exit(1);
        }
        fwrite(STDOUT, "PASS: live REST unknown-account lookup returned the documented HTTP 404.\n");
    }
}
