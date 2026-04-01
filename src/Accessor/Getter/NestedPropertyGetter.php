<?php
declare(strict_types=1);

namespace ControlBit\Dto\Accessor\Getter;

use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\GetterInterface;
use ControlBit\Dto\Contract\Transformer\TransformableInterface;

/**
 * Get Accessor that access via nested property
 */
final readonly class NestedPropertyGetter implements GetterInterface, TransformableInterface
{
    public function __construct(private string $propPath, private TypeBag $type, private AttributeBag $attributes)
    {
    }

    public function get(object $object): mixed
    {
        [$property, $current] = $this->getLeafProperty($object);

        return $property->getValue($current);
    }

    public function getAttributes(): AttributeBag
    {
        return $this->attributes;
    }

    public function hasTransformersAttributes(): bool
    {
        return $this->attributes->has(Transformer::class);
    }

    public function getTransformerAttributes(): array
    {
        return $this->attributes->getAllOf(Transformer::class);
    }

    public function getType(): TypeBag
    {
        return $this->type;
    }

    /**
     * @return array{\ReflectionProperty, mixed}
     */
    public function getLeafProperty(object $object): array
    {
        $parts   = \explode('.', $this->propPath);
        $partsCount = \count($parts);
        $current = $object;
        $lastObject = null;

        foreach ($parts as $index => $part) {
            $reflection = new \ReflectionObject($current);
            $property   = $reflection->getProperty($part);
            $property->setAccessible(true);

            $current = $property->getValue($current);

            if (\is_object($current) && ($index + 1) !== $partsCount) {
                $lastObject = $current;
            }
        }

        return [$property, $lastObject];
    }
}