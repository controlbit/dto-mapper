<?php
declare(strict_types=1);

namespace ControlBit\Dto\Transformer;

use ControlBit\Dto\Contract\Transformer\TransformerInterface;
use ControlBit\Dto\Exception\TransformerException;

final class FirstElementOfArrayTransformer implements TransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform(mixed $value, array $options = []): mixed
    {
        if (!\is_array($value)) {
            return throw new TransformerException('Value is not an array, but expected to transform to scalar.');
        }

        if (count($value) === 0) {
            return null;
        }

        return \reset($value);
    }

    /**
     * {@inheritDoc}
     */
    public function reverse(mixed $value, array $options = []): mixed
    {
        return [$value];
    }
}