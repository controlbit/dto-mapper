<?php
declare(strict_types=1);

namespace ControlBit\Dto\Transformer;

use ControlBit\Dto\Contract\Transformer\TransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;

final class FirstElementOfArrayTransformer implements TransformerInterface
{
    /**
     * @param  array<scalar>  $value
     * {@inheritDoc}
     */
    public function transform(mixed $value, array $options = []): mixed
    {
        return $value[0];
    }

    /**
     * @param  scalar  $value
     * {@inheritDoc}
     */
    public function reverse(mixed $value, array $options = []): mixed
    {
        return [$value];
    }
}