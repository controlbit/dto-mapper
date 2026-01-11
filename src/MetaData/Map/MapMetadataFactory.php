<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Map;

use ControlBit\Dto\Attribute\From;
use ControlBit\Dto\Attribute\To;
use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Property\PropertyMetadata;

final class MapMetadataFactory
{
    public function create(ClassMetadata $sourceMetadata, ClassMetadata $destinationMetadata): MapMetadataCollection
    {
        $mapMetadata = new MapMetadataCollection();

        foreach ($sourceMetadata->getProperties() as $propertyMetadata) {
            $attributes = $propertyMetadata->getAttributes();

            if ($attributes->has(To::class)) {
                $mapMetadata->merge($this->mapTo($propertyMetadata, $attributes));
            }
        }

        foreach ($destinationMetadata->getProperties() as $propertyMetadata) {
            $attributes = $propertyMetadata->getAttributes();

            $mapMetadata->merge(match (true) {
                $attributes->has(From::class) => $this->mapFrom($propertyMetadata, $attributes),
                default                       => new MapMetadata(
                    $propertyMetadata->getName(),
                    null,
                    $propertyMetadata->getName(),
                    null,
                    null,
                    $this->getTransformer($propertyMetadata)
                ),
            });
        }

        return $mapMetadata;
    }

    private function mapTo(PropertyMetadata $propertyMetadata, AttributeBag $attributes): MapMetadata
    {
        /** @var To $attribute */
        $attribute = $attributes->get(To::class);

        return new MapMetadata(
            $propertyMetadata->getName(),
            null,
            $attribute->getMember(),
            $attribute->getSetter(),
            $this->getTransformer($propertyMetadata),
            null,
        );
    }

    private function mapFrom(PropertyMetadata $propertyMetadata, AttributeBag $attributes): MapMetadata
    {
        /** @var From $attribute */
        $attribute = $attributes->get(From::class);

        return new MapMetadata(
            $attribute->getMember(),
            $attribute->getGetter(),
            $propertyMetadata->getName(),
            null,
            null,
            $this->getTransformer($propertyMetadata),
        );
    }

    private function getTransformer(PropertyMetadata $propertyMetadata): ?string
    {
        foreach ($propertyMetadata->getAttributes() as $attribute) {
            if (!is_a($attribute, Transformer::class, true)) {
                continue;
            }

            return $attribute->getTransformerIdOrClass();
        }

        return null;
    }
}