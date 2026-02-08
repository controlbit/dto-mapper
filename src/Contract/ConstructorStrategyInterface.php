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

    public function getName(): string;
}