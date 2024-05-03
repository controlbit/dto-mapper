<?php
declare(strict_types=1);

namespace ControlBit\Dto\Accessor\Getter;

use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Contract\Accessor\GetterInterface;

/**
 * Get Accessor for method name
 */
final readonly class MethodGetter implements GetterInterface
{
    public function __construct(private string $methodName, private AttributeBag $attributes)
    {
    }

    public function get(object $object): mixed
    {
        return $object->{$this->methodName}();
    }

    public function getAttributes(): AttributeBag
    {
        return $this->attributes;
    }
}