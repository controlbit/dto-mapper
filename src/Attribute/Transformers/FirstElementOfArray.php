<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute\Transformers;

use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Transformer\FirstElementOfArrayTransformer;

/**
 * Tells if it should be mapped as single element of given array
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class FirstElementOfArray extends Transformer
{
    public function __construct()
    {
        parent::__construct(FirstElementOfArrayTransformer::class);
    }
}