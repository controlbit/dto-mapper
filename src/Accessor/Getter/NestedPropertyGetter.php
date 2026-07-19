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
        $parts = \explode('.', $this->propPath);
        /** @var object $item */
        $item = $object;

        foreach ($parts as $part) {
            $item = $this->getValueOfCurrent($item, $part); // @phpstan-ignore-line
        }

        return $item;
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
     * @param  object[]|object  $current
     */
    private function getValueOfCurrent(object|array $current, string $part): mixed
    {
        if (\is_array($current)) {
            return $current[$part] ?? null;
        }

        $reflection = new \ReflectionObject($current);
        $property   = $reflection->getProperty($part);
        $property->setAccessible(true);

        return $property->getValue($current);
    }
}