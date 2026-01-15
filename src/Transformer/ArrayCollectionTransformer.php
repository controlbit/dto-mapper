<?php
declare(strict_types=1);

namespace ControlBit\Dto\Transformer;

use ControlBit\Dto\Contract\Transformer\TransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;

final class ArrayCollectionTransformer implements TransformerInterface
{
    /**
     * @param  array<int|string, mixed>  $value
     * {@inheritDoc}
     */
    public function transform(mixed $value, array $options = []): mixed
    {
        return new ArrayCollection($value);
    }

    /**
     * @param  ArrayCollection<int|string, mixed>  $value
     * {@inheritDoc}
     */
    public function reverse(mixed $value, array $options = []): mixed
    {
        return $value->toArray();
    }
}