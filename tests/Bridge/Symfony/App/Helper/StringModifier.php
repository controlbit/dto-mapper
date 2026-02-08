<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Bridge\Symfony\App\Helper;

abstract class StringModifier
{
    public static function modify(string $value, ?string $prefix = null, ?string $suffix = null): string
    {
        return $prefix . \strtoupper($value) . $suffix;
    }
}