<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute\Transformers;

use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Transformer\FileBase64Transformer;

/**
 * Tells if should be mapped as doctrine collection
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class FileBase64 extends Transformer
{
    public function __construct($options = [])
    {
        parent::__construct(FileBase64Transformer::class, $options);
    }
}