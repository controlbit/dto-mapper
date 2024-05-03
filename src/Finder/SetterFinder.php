<?php
declare(strict_types=1);

namespace ControlBit\Dto\Finder;

use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\SetterResolverInterface;
use ControlBit\Dto\Exception\SetterNotFoundException;
use ControlBit\Dto\MetaData\ObjectMetadata;
use ControlBit\Dto\MetaData\PropertyMetadata;

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

    public function find(PropertyMetadata $sourcePropertyMetadata, ObjectMetadata $destinationMetadata): ?SetterInterface
    {
        foreach ($this->resolvers as $resolver) {
            $setter = $resolver->resolve($sourcePropertyMetadata, $destinationMetadata);

            if (null === $setter) {
                continue;
            }

            return $setter;
        }

        // TODO: Log
        return null;
    }
}