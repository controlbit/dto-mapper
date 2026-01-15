<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract\Transformer;

interface TransformerInterface
{
    /**
     * Used when transforming from Something To DTO.
     * When you for example want to transform to Enum using Enum Transformer,
     * it's meant that THIS method transform To string, which DTO will use
     */
    public function transform(mixed $value, array $options = []): mixed;


    /**
     * Used if needed to transform in Reverse.
     * If nor reverse in options is set explicitly, it will be used when transforming from Array
     */
    public function reverse(mixed $value, array $options = []): mixed;
}