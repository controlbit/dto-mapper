<?php
declare(strict_types=1);

namespace ControlBit\Dto\Transformer;

use ControlBit\Dto\Contract\Transformer\TransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;

final class ArrayCollectionTransformer implements TransformerInterface
{
    /**
     * @template T
     * @param  ArrayCollection<int|string, T>  $value
     *
     * @return array<T>
     * {@inheritDoc}
     */
    public function transform(mixed $value, array $options = []): mixed
    {
        return new ArrayCollection($value);
    }

    /**
     * @template T
     * @param  array<int|string, T>  $value
     *
     * @return ArrayCollection<int|string, T>
     * {@inheritDoc}
     */
    public function reverse(mixed $value, array $options = []): mixed
    {
        return $value->toArray();
    }
}