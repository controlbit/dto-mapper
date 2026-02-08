<?php
declare(strict_types=1);

namespace ControlBit\Dto\Finder;

use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\SetterResolverInterface;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadata;
use ControlBit\Dto\MetaData\Property\PropertyMetadata;

/**
 * Resolves/finds which setter to use by given
 * Source Property metadata and destination object metadata
 * by calling dedicated setter resolvers in order to get one.
 */
final readonly class SetterFinder
{
    /**
     * @param  iterable<SetterResolverInterface>  $resolvers
     */
    public function __construct(private iterable $resolvers)
    {
    }

    /**
     * @template T of object
     * @param  ClassMetadata<T>  $destinationMetadata
     *
     * @return SetterInterface|null
     */
    public function find(ClassMetadata $destinationMetadata, MapMetadata $memberMapMetadata): ?SetterInterface
    {
        foreach ($this->resolvers as $resolver) {
            $setter = $resolver->resolve($destinationMetadata, $memberMapMetadata);

            if (null === $setter) {
                continue;
            }

            return $setter;
        }

        // TODO: Log
        return null;
    }
}