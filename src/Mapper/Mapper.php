<?php
declare(strict_types=1);

namespace ControlBit\Dto\Mapper;

use ControlBit\Dto\Builder\Builder;
use ControlBit\Dto\Contract\Mapper\MapAdapterInterface;
use ControlBit\Dto\Contract\Mapper\MapperInterface;
use ControlBit\Dto\Exception\MapAdapterNotFound;
use ControlBit\Dto\Finder\SetterFinder;
use ControlBit\Dto\MetaData\ObjectMetadataFactory;

final readonly class Mapper implements MapperInterface
{
    public function __construct(
        private ObjectMetadataFactory $objectMetadataFactory,
        private MapAdapterInterface   $mapAdapter,
        private SetterFinder          $setterFinder,
        private Builder               $builder,
    ) {
    }

    public function map(mixed $source, mixed $destination = null): object|false
    {
        if (!$this->mapAdapter->adapt($source, $destination)) {
            throw new MapAdapterNotFound('Unable determine what two objects to map.');
        }

        $sourceMetadata      = $this->objectMetadataFactory->create($source);
        $destinationMetadata = $this->objectMetadataFactory->create($destination);

        foreach ($sourceMetadata->getProperties() as $propertyMetadata) {
            $propertyMetadata->setDestinationSetter(
                $this->setterFinder->find($propertyMetadata, $destinationMetadata)
            );

        }

        return $this->builder
            ->withSourceMetadata($sourceMetadata)
            ->withDestinationMetadata($destinationMetadata)
            ->build($source, $destination, $this)
        ;
    }
}