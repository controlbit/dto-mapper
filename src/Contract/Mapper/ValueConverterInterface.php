<?php

namespace ControlBit\Dto\Contract\Mapper;

use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\MetaData\PropertyMetadata;

interface ValueConverterInterface
{
    public function supports(object $source, PropertyMetadata $sourcePropertyMetadata, mixed $value): bool;

    public function execute(Mapper $mapper, PropertyMetadata $sourcePropertyMetadata, mixed $value): mixed;
}