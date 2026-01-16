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

    public function add(MapMetadata $mapMetadata): void
    {
        $this->collection[] = $mapMetadata;
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

    public function getIterator(): \Traversable
    {
        yield from $this->collection;
    }

    public function count(): int
    {
        return \count($this->collection);
    }
}