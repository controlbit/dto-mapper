<?php
declare(strict_types=1);

namespace ControlBit\Dto\Transformer;

use ControlBit\Dto\Contract\Transformer\TransformerInterface;
use ControlBit\Dto\Exception\InvalidArgumentException;

final class EnumTransformer implements TransformerInterface
{
    /**
     * @param  \BackedEnum  $value
     *
     * @return string|int
     * {@inheritDoc}
     */
    public function transform(mixed $value, array $options = []): mixed
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof \BackedEnum) {
            throw new InvalidArgumentException(
                'Provided value for Enum Transformer is not Backed Enum, although fromEnum is set to true.'
            );
        }

        return $value->value;
    }

    /**
     * @param  string|int  $value
     *
     * @return \BackedEnum
     * {@inheritDoc}
     */
    public function reverse(mixed $value, array $options = []): mixed
    {
        if (null === $value) {
            return null;
        }

        if (!\is_string($value) && !\is_int($value)) {
            throw new InvalidArgumentException(
                'Provided value for Enum Transformer is not a string or integer, although toEnum is set to true.'
            );
        }

        $class                    = $this->getClass($options);
        $enumValue                = ($class)::tryFrom($value);
        $failWhenInvalidEnumValue = $options['failWhenInvalidEnumValue'] ?? false;

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

    private function getClass(array $options): string
    {
        $class = $options['class'] ?? null;

        if (null === $class) {
            throw new InvalidArgumentException('Class option is required for Enum Transformer.');
        }

        if (!\class_exists($class)) {
            throw new InvalidArgumentException(
                \sprintf('Class "%s" does not exist.', $class)
            );
        }

        return $class;
    }
}