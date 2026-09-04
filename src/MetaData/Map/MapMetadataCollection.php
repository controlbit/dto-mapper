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

    /**
     * @var array<string, MapMetadata>
     */
    private array $byDestinationMember = [];

    public function add(MapMetadata $mapMetadata): void
    {
        $this->collection[] = $mapMetadata;

        $destinationMember = $mapMetadata->getDestinationMember();

        if (null !== $destinationMember) {
            $this->byDestinationMember[$destinationMember] = $mapMetadata;
        }
    }

    public function getHavingDestinationMember(string $member): ?MapMetadata
    {
        return $this->byDestinationMember[$member] ?? null;
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