<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute;

/**
 * Marks DTO object to be by default mapped to Doctrine Entity
 * Used in pair with #[Identifier] attribute to mark Identifier of entity.
 * If set, Mapper will first fetch Entity via ORM, but will not persist it.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Entity
{
    /**
     * @param  class-string  $target
     */
    public function __construct(public string $target)
    {
    }

    /**
     * @return class-string
     */
    public function getTarget(): string
    {
        return $this->target;
    }
}