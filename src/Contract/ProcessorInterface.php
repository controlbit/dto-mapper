<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract;

/**
 * @psalm-type AssociativeArray = array<int|string, mixed>
 */
interface ProcessorInterface
{
    /**
     * @param  object|array<string,mixed>  $source  The source data - either an object or associative array
     */
    public function process(object|array $source, object $dto): void;
}