<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute\Transformers;

use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Transformer\UuidTransformer;

/**
 * Transforms to and from Enum
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class Uuid extends Transformer
{
    public function __construct(array $options = [])
    {
        parent::__construct(UuidTransformer::class, $options);
    }
}