<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract;

use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;

interface ConstructorStrategyInterface
{
    /**
     * @param  \ReflectionClass<object>  $destinationReflectionClass
     */
    public function validate(
        \ReflectionClass      $destinationReflectionClass,
        MapMetadataCollection $mapMetadata,
    ): void;

    /**
     * @param  \ReflectionClass<object>  $destinationReflectionClass
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