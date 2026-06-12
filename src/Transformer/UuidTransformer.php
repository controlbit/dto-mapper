<?php

declare(strict_types=1);

namespace ControlBit\Dto\Transformer;

use ControlBit\Dto\Contract\Transformer\TransformerInterface;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\Exception\RuntimeException;
use Symfony\Component\Uid\Uuid;

final class UuidTransformer implements TransformerInterface
{
    /**
     * @param  mixed  $value
     *
     * @return string|null
     * {@inheritDoc}
     */
    public function transform(mixed $value, array $options = []): mixed
    {
        if (null === $value) {
            return null;
        }

        if (!class_exists('Symfony\Component\Uid\Uuid')) {
            throw new RuntimeException(
                'You have to install the "symfony/uuid" composer package to use Uuid transformer for Dto.'
            );
        }

        if (!$value instanceof Uuid) {
            throw new InvalidArgumentException(
                'Provided value for Uuid Transformer is not Uuid.'
            );
        }

        return $value->toRfc4122();
    }

    /**
     * @param  mixed  $value
     *
     * @return Uuid|null
     * {@inheritDoc}
     */
    public function reverse(mixed $value, array $options = []): mixed
    {
        if (null === $value) {
            return null;
        }

        if (!\is_string($value)) {
            throw new InvalidArgumentException(
                'Provided value for Uuid transformer is not a string.'
            );
        }

        if (!Uuid::isValid($value)) {
            throw new InvalidArgumentException(
                'Provided value for Uuid transformer is not a valid uuid.'
            );
        }

        return Uuid::fromString($value);
    }
}