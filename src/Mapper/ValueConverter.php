<?php
declare(strict_types=1);

namespace ControlBit\Dto\Mapper;

use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\ValueConverterInterface;
use ControlBit\Dto\MetaData\ObjectMetadata;
use ControlBit\Dto\MetaData\PropertyMetadata;

final readonly class ValueConverter
{
    /**
     * @param  iterable<ValueConverterInterface>  $valueConverters
     */
    public function __construct(private iterable $valueConverters)
    {
    }

    public function map(
        Mapper $mapper,
        object $source,
        ObjectMetadata $sourceMetadata,
        PropertyMetadata $sourcePropertyMetadata,
        SetterInterface $destinationSetter,
    ): mixed
    {
        $value = $this->transform($source, $sourceMetadata, $sourcePropertyMetadata, $destinationSetter);

        foreach ($this->valueConverters as $valueConverter) {
            if (!$valueConverter->supports($source, $sourcePropertyMetadata, $value)) {
                continue;
            }

            return $valueConverter->execute($mapper, $sourcePropertyMetadata, $value);
        }

        return $value;
    }

    private function transform(
        object $source,
        ObjectMetadata $sourceMetadata,
        PropertyMetadata $sourcePropertyMetadata,
        SetterInterface $destinationSetter,
    ): mixed
    {
        $attributes = $sourcePropertyMetadata->getAttributes();

        // Source is Doctrine Entity, Destination is DTO
        if ($sourceMetadata->isDoctrineEntity()) {
            $attributes = $destinationSetter->getAttributes();
        }

        $value      = $sourcePropertyMetadata->getAccessor()->get($source);

        foreach ($attributes as $attribute) {
            if (!$attribute instanceof Transformer) {
                continue;
            }

            if ($sourceMetadata->isDoctrineEntity()) {
                return $attribute->getTransformerClass()::reverse($value);
            }

            return $attribute->getTransformerClass()::transform($value);
        }

        return $value;
    }
}