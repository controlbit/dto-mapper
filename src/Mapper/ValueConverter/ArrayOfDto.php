<?php
declare(strict_types=1);

namespace ControlBit\Dto\Mapper\ValueConverter;

use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\Contract\Mapper\ValueConverterInterface;
use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\MetaData\PropertyMetadata;

/**
 *  Case when we have an iterable, and property to map to is
 *  marked as array of dto by #[Dto] Attribute.
 */
final class ArrayOfDto implements ValueConverterInterface
{
    public function supports(object $source, PropertyMetadata $sourcePropertyMetadata, mixed $value): bool
    {
        if (\is_object($value) || !\is_iterable($value)) {
            return false;
        }

        return $sourcePropertyMetadata->getDestinationSetter()?->getAttributes()->has(Dto::class) ?? false;
    }

    /**
     * @param  iterable<mixed>|array<mixed>  $value
     */
    public function execute(Mapper $mapper, PropertyMetadata $sourcePropertyMetadata, mixed $value): mixed
    {
        /** @var Dto $destinationDtoAttribute */
        $destinationDtoAttribute = $sourcePropertyMetadata->getDestinationSetter()?->getAttributes()->get(Dto::class);

        return \array_map(
            static fn($item) => $mapper->map($item, $destinationDtoAttribute->getClass()),
            [...$value],
        );
    }
}