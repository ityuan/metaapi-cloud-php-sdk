<?php

namespace Victorycodedev\MetaapiCloudPhpSdk\Resources;

/** Encodes a single path segment without encoding the surrounding URL structure. */
final class Path
{
    public static function segment(string|int $value): string
    {
        return rawurlencode((string) $value);
    }
}
