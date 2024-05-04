<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute\Transformers;

use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Transformer\ArrayCollectionTransformer;

/**
 * Tells if should be mapped as doctrine collection
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class Collection extends Transformer
{
    public function __construct()
    {
        parent::__construct(ArrayCollectionTransformer::class);
    }
}