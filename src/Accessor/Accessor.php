<?php
declare(strict_types=1);

namespace ControlBit\Dto\Accessor;

use ControlBit\Dto\Contract\Accessor\AccessorInterface;
use ControlBit\Dto\Contract\Accessor\GetterInterface;
use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Exception\RuntimeException;

final class Accessor implements AccessorInterface
{
    private ?SetterInterface $setter;
    private ?GetterInterface $getter;

    public function __construct(?SetterInterface $setter, ?GetterInterface $getter)
    {
        $this->setter = $setter;
        $this->getter = $getter;
    }

    public function set(object $object, mixed $value): void
    {
        if (null !== $this->setter) {
            $this->setter->set($object, $value);
        }
    }

    public function get(object $object): mixed
    {
        if (null === $this->getter) {
            throw new RuntimeException(
                \sprintf('Getter now available for getting property on object of "%s"', \get_class($object))
            );
        }

        return $this->getter->get($object);
    }

    public function getSetter(): ?SetterInterface
    {
        return $this->setter;
    }

    public function getGetter(): ?GetterInterface
    {
        return $this->getter;
    }


}