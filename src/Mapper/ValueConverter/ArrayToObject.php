<?php
declare(strict_types=1);

namespace ControlBit\Dto\Mapper\ValueConverter;

use ControlBit\Dto\Contract\Mapper\ValueConverterInterface;
use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\MetaData\PropertyMetadata;

/**
 * Usual case when we have Array on one side, and we need to map to object props on another
 */
final class ArrayToObject implements ValueConverterInterface
{
    public function supports(object $source, PropertyMetadata $sourcePropertyMetadata, mixed $value): bool
    {
        if (\is_object($value) || !\is_iterable($value)) {
            return false;
        }

        $type = $sourcePropertyMetadata->getDestinationSetter()?->getType();

        return null !== $type && $type->hasOneClass();
    }

    public function execute(Mapper $mapper, PropertyMetadata $sourcePropertyMetadata, mixed $value): mixed
    {
        return $mapper->map($value, $sourcePropertyMetadata->getDestinationSetter()?->getType()->getOneClass());
    }
}