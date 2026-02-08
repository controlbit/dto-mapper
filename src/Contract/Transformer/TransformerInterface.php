<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract\Transformer;

/**
 * @psalm-type AssociativeArray = array<int|string, mixed>
 */
interface TransformerInterface
{
    /**
     * Used when transforming from Something To DTO.
     * When you, for example, want to transform to Enum using Enum Transformer,
     * it's meant that THIS method transform To string, which DTO will use
     *
     * @param  AssociativeArray  $options
     */
    public function transform(mixed $value, array $options = []): mixed;


    /**
     * Used if needed to transform in Reverse.
     * If nor reverse in options is set explicitly, it will be used when transforming from Array
     *
     * @param  AssociativeArray  $options
     */
    public function reverse(mixed $value, array $options = []): mixed;
}