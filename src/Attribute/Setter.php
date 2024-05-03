<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute;

/**
 * Defines a setter to use when setting value
 * from SOURCE  to DESTINATION (to, mapped) object.
 * Setter will be called on DESTINATION object.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class Setter
{
    public function __construct(private string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }
}