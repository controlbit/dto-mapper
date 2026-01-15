<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Map;

/**
 * @implements \IteratorAggregate<MapMetadata>
 */
final class MapMetadataCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var MapMetadata[]
     */
    private array $collection = [];

    public function merge(MapMetadata $mapMetadata): void
    {
        $existingMapMetadata = null;

        if ($mapMetadata->getSourceMember()) {
            $existingMapMetadata = $this->getHavingDestinationMember($mapMetadata->getSourceMember());
        }

        if (null === $existingMapMetadata && $mapMetadata->getDestinationMember()) {
            $existingMapMetadata = $this->getHavingDestinationMember($mapMetadata->getDestinationMember());
        }

        if (null === $existingMapMetadata) {
            $this->add($mapMetadata);

            return;
        }

        $mergedMetadata = new MapMetadata(
            $existingMapMetadata->getSourceMember() ?? $mapMetadata->getSourceMember(),
            $existingMapMetadata->getSourceMethod() ?? $mapMetadata->getSourceMethod(),
            $existingMapMetadata->getDestinationMember() ?? $mapMetadata->getDestinationMember(),
            $existingMapMetadata->getDestinationMethod() ?? $mapMetadata->getDestinationMethod(),
            $existingMapMetadata->isMappedInConstructor() || $mapMetadata->isMappedInConstructor(),
        );

        $this->remove($existingMapMetadata);

        $this->add($mergedMetadata);
    }

    private function add(MapMetadata $mapMetadata): void
    {
        $this->collection[] = $mapMetadata;
    }

    /**
     * @return MapMetadata[]
     */
    public function getAll(): array
    {
        return $this->collection;
    }

    public function getHavingDestinationMember(string $member): ?MapMetadata
    {
        foreach ($this->collection as $mapMetadata) {
            if ($mapMetadata->getDestinationMember() === $member) {
                return $mapMetadata;
            }
        }

        return null;
    }

    public function getHavingSourceMember(string $member): ?MapMetadata
    {
        foreach ($this->collection as $mapMetadata) {
            if ($mapMetadata->getSourceMember() === $member) {
                return $mapMetadata;
            }
        }

        return null;
    }

    public function getIterator(): \Traversable
    {
        yield from $this->collection;
    }

    public function count(): int
    {
        return \count($this->collection);
    }

    private function remove(MapMetadata $mapMetadata): void
    {
        $key = \array_search($mapMetadata, $this->collection, true);
        if (false !== $key) {
            unset($this->collection[$key]);
        }
    }
}