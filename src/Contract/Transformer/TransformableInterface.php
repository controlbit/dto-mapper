<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract\Transformer;

interface TransformableInterface
{
    /**
     * @return string|null
     */
    public function getClassOrId(): ?string;

    public function getOptions(): ?array;

    public function hasTransformer(): bool;
}