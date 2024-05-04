<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute;

/**
 * Denotes when Given Object is DTO, on property, when mapping, or
 * with argument, on Controller injection, when using Symfony.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Dto
{
    /**
     * @param  class-string|null  $class
     */
    public function __construct(public ?string $class = null)
    {
    }

    /**
     * @return class-string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }
}