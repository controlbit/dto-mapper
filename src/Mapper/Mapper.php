<?php
declare(strict_types=1);

namespace ControlBit\Dto\Mapper;

use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\Contract\Mapper\MapperInterface;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\Exception\PropertyMapException;
use ControlBit\Dto\Destination\DestinationFactory;
use ControlBit\Dto\Finder\AccessorFinder;
use ControlBit\Dto\Finder\SetterFinder;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Class\ClassMetadataFactory;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;
use ControlBit\Dto\MetaData\Map\MapMetadataFactory;
use ControlBit\Dto\Util\Initializer;

final readonly class Mapper implements MapperInterface
{
    public function __construct(
        private ClassMetadataFactory $objectMetadataFactory,
        private MapMetadataFactory   $mapMetadataFactory,
        private DestinationFactory   $destinationFactory,
        private ValueConverter       $valueConverter,
        private SetterFinder         $setterFinder,
        private AccessorFinder       $accessorFinder,
    ) {
    }

    public function mapCollection(array $source, string $destination = null): array
    {
        return \array_map(fn($item) => $this->map($item, $destination), $source);
    }

    public function map(object|array $source, string|object|null $destination = null): object
    {
        // Preparing source object. It must be objected to.
        $source = \is_object($source) ? $source : (object)$source;

        \assert(\is_object($source));

        // Fetching source metadata. This could be cached. But not in case of stdClass.
        $sourceMetadata = $this->objectMetadataFactory->create($source);

        if (null === $destination) {
            $destination = $sourceMetadata->getAttributes()->get(Dto::class)?->getEntityClass();

            if (null === $destination) {
                throw new InvalidArgumentException('Unknown Destination class/object. At least add #[Dto] on Source class.');
            }
        }

        $destinationMetadata = $this->objectMetadataFactory->create($destination);
        $mapMetadata         = $this->mapMetadataFactory->create($sourceMetadata, $destinationMetadata);

        // Auto-initialize values that are not initialized in a source object.
        Initializer::autoInitialize($source, $sourceMetadata);

        if (!\is_object($destination)) {
            $destination = $this->destinationFactory->create(
                $this,
                $source,
                $sourceMetadata,
                $mapMetadata,
                $destination
            );
        }

        $destination = $this->execute(
            $source,
            $destination,
            $sourceMetadata,
            $destinationMetadata,
            $mapMetadata,
        );

        Initializer::autoInitialize($destination, $destinationMetadata);

        return $destination;
    }

    private function execute(
        object                $source,
        object                $destination,
        ClassMetadata         $sourceMetadata,
        ClassMetadata         $destinationMetadata,
        MapMetadataCollection $mapMetadataCollection,
    ): object {
        foreach ($mapMetadataCollection as $mapMetadata) {
            if ($mapMetadata->isMappedInConstructor()) {
                continue;
            }

            $setter = $this->setterFinder->find($destinationMetadata, $mapMetadata);

            if (null === $setter) {
                continue;
            }

            $sourceReflection = new \ReflectionObject($source);
            $getter = $this->accessorFinder->findGetter($sourceReflection, $mapMetadata);

            if (null === $getter) {
                continue;
            }

            $value = $getter->get($source);
            $value = $this->valueConverter->map($this, $sourceMetadata, $getter, $setter, $value);

            try {
                $setter->set($destination, $value);
            } catch (\Throwable $e) {
                throw new PropertyMapException($mapMetadata, $sourceMetadata, $destinationMetadata, $setter, $getter, $e);
            }
        }

        return $destination;
    }
}