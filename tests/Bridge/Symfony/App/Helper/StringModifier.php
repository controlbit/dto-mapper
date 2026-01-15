<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Bridge\Symfony\App\Helper;

abstract class StringModifier
{
    public static function toUpperCase(string $value): string
    {
        return \strtoupper($value);
    }
}