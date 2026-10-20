# Custom HTTP Client

You can inject a custom Guzzle client when constructing the SDK:

```php
use GuzzleHttp\Client;
use Victorycodedev\MetaapiCloudPhpSdk\MetaApiClient;

$guzzle = new Client([
    'timeout' => 30,
    'connect_timeout' => 10,
]);

$metaapi = new MetaApiClient('AUTH_TOKEN', $guzzle);
```

The SDK still applies the required MetaApi headers automatically:

- `auth-token`
- `Accept: application/json`
- `Content-Type: application/json` for JSON requests

The SDK also normalizes HTTP errors and transport failures to `MetaApiException`, even when a custom Guzzle client is used.

When the SDK creates its own Guzzle client, it uses a 60 second overall timeout and a 10 second connection timeout. If you inject a client, the SDK preserves that client's configured timeout defaults. Configure the client explicitly for your workload; long-running historical market-data calls may need a larger overall timeout.
