<?php

namespace Victorycodedev\MetaapiCloudPhpSdk\Resources\Manager;

final class Path
{
    public static function segment(string|int $value): string
    {
        return \Victorycodedev\MetaapiCloudPhpSdk\Resources\Path::segment($value);
    }
}
