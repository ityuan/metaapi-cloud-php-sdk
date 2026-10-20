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


## Timeouts, transaction IDs and retries

The SDK's default Guzzle client uses a 30-second total request timeout and a 10-second connection timeout. When constructing Http directly, these defaults can be overridden with the fourth clientOptions argument. A caller-supplied ClientInterface keeps its own configuration; configure timeouts on that client before injecting it.

Account creation and replica creation generate a transaction ID when one is not supplied. The resulting ActionResponse::transactionId() exposes the exact value used so a caller can reuse it when following up on a 202 Accepted response.

The SDK does not automatically retry requests. In particular, do not resend account-creation, replica-creation, trade, or other side-effecting POST requests just because a timeout occurred: the server may have processed the request before the connection failed. For account creation, follow the MetaApi transaction-ID contract and reuse the same transaction ID to check the original operation. ActionResponse::shouldRetry() is legacy response metadata, not a command to repeat the original POST blindly.

Successful JSON object/array responses are decoded as arrays. JSON string scalars are returned as strings; numeric, boolean and null JSON scalar bodies are preserved as their raw JSON text to honor the SDK's array|string|null return contract. Non-JSON bodies are returned as strings.
