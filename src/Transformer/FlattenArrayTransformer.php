<?php
declare(strict_types=1);

namespace ControlBit\Dto\Transformer;

use ControlBit\Dto\Contract\Transformer\TransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;

final class FlattenArrayTransformer implements TransformerInterface
{
    /**
     * @param  array<scalar>  $value
     * @return scalar
     * {@inheritDoc}
     */
    public function transform(mixed $value, array $options = []): mixed
    {
        return $value[0];
    }

    /**
     * @param  scalar  $value
     * @return array<scalar>
     * {@inheritDoc}
     */
    public function reverse(mixed $value, array $options = []): mixed
    {
        return [$value];
    }
}