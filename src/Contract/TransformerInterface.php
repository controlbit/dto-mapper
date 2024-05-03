<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract;

interface TransformerInterface
{
    /**
     * Used when transforming from DTO to DESTINATION-OBJECT
     */
    public static function transform(mixed $value): mixed;

    /**
     * Used when transforming from DOCTRINE-ENTITY to DTO
     */
    public static function reverse(mixed $value): mixed;
}