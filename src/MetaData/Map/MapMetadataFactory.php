<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Map;

use ControlBit\Dto\Attribute\From;
use ControlBit\Dto\Attribute\To;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Property\PropertyMetadata;

final class MapMetadataFactory
{
    public function create(ClassMetadata $sourceMetadata, ClassMetadata $destinationMetadata): MapMetadataCollection
    {
        $mapMetadata               = new MapMetadataCollection();
        $visitedDestinationMembers = [];

        foreach ($sourceMetadata->getProperties() as $propertyMetadata) {
            $attributes = $propertyMetadata->getAttributes();

            if (!$attributes->has(To::class)) {
                continue;
            }

            $to     = $attributes->get(To::class);
            $member = $to?->getMember();

            if (\in_array($member, $visitedDestinationMembers, true)) {
                continue;
            }

            $visitedDestinationMembers[] = $member;
            $mapMetadata->add($this->mapTo($propertyMetadata, $attributes));
        }

        foreach ($destinationMetadata->getProperties() as $propertyMetadata) {
            $attributes = $propertyMetadata->getAttributes();
            $member     = $propertyMetadata->getName();

            if (\in_array($member, $visitedDestinationMembers, true)) {
                continue;
            }

            $mapMetadata->add(match (true) {
                $attributes->has(From::class) => $this->mapFrom($propertyMetadata, $attributes),
                default                       => new MapMetadata($member, null, $member, null),
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
        );
    }
}