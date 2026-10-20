<?php

namespace Victorycodedev\MetaapiCloudPhpSdk\Resources;

/**
 * Encodes one dynamic URL path segment without changing path separators.
 */
final class Path
{
    public static function segment(string|int $value): string
    {
        return rawurlencode((string) $value);
    }
}
