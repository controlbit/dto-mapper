<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract\Transformer;

use ControlBit\Dto\Attribute\Transformer;

interface TransformableInterface
{
    /**
     * @return Transformer[]
     */
    public function getTransformerAttributes(): array;

    public function hasTransformersAttributes(): bool;
}