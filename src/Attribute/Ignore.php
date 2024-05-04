<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute;

/**
 * Marks property of entity to be Ignored in mapping FROM, to another object.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
final class Ignore
{
    public function __construct()
    {
    }
}