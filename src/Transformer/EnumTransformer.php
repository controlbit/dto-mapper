<?php
declare(strict_types=1);

namespace ControlBit\Dto\Transformer;

use ControlBit\Dto\Contract\Transformer\TransformerInterface;
use ControlBit\Dto\Exception\InvalidArgumentException;

final class EnumTransformer implements TransformerInterface
{
    /**
     * @param  string|int  $value
     * {@inheritDoc}
     */
    public function transform(mixed $value, array $options = []): mixed
    {
        if (null === $value) {
            return null;
        }

        return $this->process($value, $options);
    }

    /**
     * @param  string|int  $value
     * {@inheritDoc}
     */
    public function reverse(mixed $value, array $options = []): mixed
    {
        if (null === $value) {
            return null;
        }

        return $this->process($value);
    }

    private function process(mixed $value, array $options = [])
    {
        $class                    = $options['class'] ?? null;
        $toEnum                   = $options['toEnum'] ?? false;
        $fromEnum                 = $options['fromEnum'] ?? false;
        $failWhenInvalidEnumValue = $options['failWhenInvalidEnumValue'] ?? false;

        if (null === $class) {
            throw new InvalidArgumentException('Class option is required for Enum Transformer.');
        }

        if (!\class_exists($class)) {
            throw new InvalidArgumentException(
                \sprintf('Class "%s" does not exist.', $class)
            );
        }

        if (!\is_string($value) && !\is_int($value) && $toEnum) {
            throw new InvalidArgumentException(
                'Provided value for Enum Transformer is not a string or integer, although toEnum is set to true.'
            );
        }

        if (!$value instanceof \BackedEnum && $fromEnum) {
            throw new InvalidArgumentException(
                'Provided value for Enum Transformer is not Backed Enum, although fromEnum is set to true.'
            );
        }

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        $enumValue = ($class)::tryFrom($value);

        if (null === $enumValue && $failWhenInvalidEnumValue) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Invalid enum value "%s" provided for enum class "%s".',
                    $value,
                    $class,
                )
            );
        }

        return $enumValue;
    }
}