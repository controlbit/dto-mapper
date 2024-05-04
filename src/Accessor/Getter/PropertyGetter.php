<?php
declare(strict_types=1);

namespace ControlBit\Dto\Accessor\Getter;

use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Contract\Accessor\GetterInterface;

/**
 * Get Accessor that access via property
 */
final readonly class PropertyGetter implements GetterInterface
{
    public function __construct(private string $propName, private AttributeBag $attributes)
    {
    }

    public function get(object $object): mixed
    {
        return $object->{$this->propName};
    }

    public function getAttributes(): AttributeBag
    {
        return $this->attributes;
    }
}