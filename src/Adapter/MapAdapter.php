<?php
declare(strict_types=1);

namespace ControlBit\Dto\Adapter;

use ControlBit\Dto\Contract\Mapper\MapAdapterInterface;

final readonly class MapAdapter implements MapAdapterInterface
{
    /**
     * @param  iterable<MapAdapterInterface>  $adapters
     */
    public function __construct(private iterable $adapters)
    {
    }

    public function adapt(mixed &$source, mixed &$destination): bool
    {
        foreach ($this->adapters as $adapter) {
            if (true === $adapter->adapt($source, $destination)) {
                return true;
            }
        }

        return false;
    }
}