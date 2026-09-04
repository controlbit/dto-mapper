<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract;

use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;

interface ConstructorStrategyInterface
{
    /**
     * @template T of object
     * @param  \ReflectionClass<T>  $destinationReflectionClass
     */
    public function validate(
        \ReflectionClass      $destinationReflectionClass,
        MapMetadataCollection $mapMetadata,
    ): void;

    /**
     * @template S of object
     * @template D of object
     * @param  \ReflectionClass<D>  $destinationReflectionClass
     * @param ClassMetadata<S> $sourceMetadata
     *
     * @return D
     */
    public function create(
        Mapper                $mapper,
        object                $source,
        ClassMetadata         $sourceMetadata,
        MapMetadataCollection $mapMetadata,
        \ReflectionClass      $destinationReflectionClass,
    ): object;

    /**
     * Cheap, side-effect-free check for whether this strategy can actually produce
     * a destination instance for the given source/destination pair. Must not throw
     * and must not do any of the work `create()` would do (no value conversion).
     *
     * @template S of object
     * @template D of object
     * @param  ClassMetadata<S>      $sourceMetadata
     * @param  \ReflectionClass<D>  $destinationReflectionClass
     */
    public function supports(
        ClassMetadata         $sourceMetadata,
        MapMetadataCollection $mapMetadata,
        \ReflectionClass      $destinationReflectionClass,
    ): bool;

    public function getName(): string;
}