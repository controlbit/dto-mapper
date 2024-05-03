<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract\Mapper;

interface MapperInterface
{
    public function map(mixed $source, mixed $destination = null): object|false;
}