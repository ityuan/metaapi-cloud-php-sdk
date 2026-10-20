# Error Handling

```php
use Victorycodedev\MetaapiCloudPhpSdk\Exceptions\MetaApiException;

try {
    $account = $metaapi->accounts()->readById('account-id');
} catch (MetaApiException $exception) {
    echo $exception->message();
    echo $exception->statusCode();
    echo $exception->id();
    echo $exception->error();

    print_r($exception->details());
    print_r($exception->toArray());
    print_r($exception->response());
}
```

`MetaApiException` reflects MetaApi's standard REST error model:

```php
$exception->id();       // Error id
$exception->error();    // Error name, e.g. ValidationError
$exception->message();  // User-friendly error description
$exception->details();  // Additional validation details
$exception->toArray();  // id, error, message and details
```

The existing `errorId()`, `errorName()` and `getMessage()` methods remain available for backward compatibility. HTTP response errors and transport failures from custom Guzzle clients are normalized to `MetaApiException`; the original transport exception is available through `getPrevious()`.

## Timeouts

The HTTP layer applies a 10 second connection timeout and a 60 second overall request timeout by default. These defaults help prevent an unavailable API from occupying a worker indefinitely. Historical market-data and other long-running endpoints may require a larger timeout; when using the low-level `Http` client, override the constructor values or explicit `timeout` and `connect_timeout` options passed to `request()`.

```php
use Victorycodedev\MetaapiCloudPhpSdk\Http;

$http = new Http(
    token: $token,
    baseUrl: $serverUrl,
    timeout: 120.0,
    connectTimeout: 5.0,
);
```

## Retries and side effects

The SDK does not automatically retry requests. A network timeout or HTTP 5xx response does not prove that the server did not process a create, trade, delete, or other operation with side effects. Blind retries can duplicate actions.

For account creation and replica creation, MetaApi uses the `transaction-id` header. When an action response is `202 Accepted`, use `ActionResponse::transactionId()` and follow the official API flow with the same transaction ID and original payload after the server's indicated delay. Do not generate a new transaction ID for that follow-up request.

For rate limits, inspect `MetaApiException::retryAfter()` and apply the delay recommended by the API. Any retry policy added by an application should be endpoint-aware and must not blindly replay side-effecting requests.
