<?php
declare(strict_types=1);

namespace ControlBit\Dto\Bag;

use Traversable;

/**
 * @implements \IteratorAggregate<Object>
 */
final class AttributeBag implements \IteratorAggregate
{
    /**
     * @var Object[]
     */
    private array $attributes = [];

    public function add(object $attribute): self
    {
        $this->attributes[] = $attribute;

        return $this;
    }

    /**
     * @param  class-string  $instanceClass
     */
    public function has(string $instanceClass): bool
    {
        foreach ($this->attributes as $attribute) {
            if (\get_class($attribute) === $instanceClass) {
                return true;
            }

            if (\is_subclass_of($attribute, $instanceClass)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @template T of object
     * @param  class-string<T>  $instanceClass
     *
     * @return T|null
     */
    public function get(string $instanceClass): ?object
    {
        foreach ($this->attributes as $attribute) {
            if (\get_class($attribute) === $instanceClass) {
                return $attribute;
            }

            if (\is_subclass_of($attribute, $instanceClass)) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * @template T of object
     * @param  class-string<T>  $instanceClass
     *
     * @return T[]
     */
    public function getAllOf(string $instanceClass): array
    {
        $attributes = [];
        foreach ($this->attributes as $attribute) {
            if (\get_class($attribute) === $instanceClass) {
                $attributes[] = $attribute;
            }

            if (\is_subclass_of($attribute, $instanceClass)) {
                $attributes[] = $attribute;
            }
        }

        return $attributes;
    }

    /**
     * @param  object[]  $attributes
     */
    public static function fromArray(array $attributes): self
    {
        $self = new self();
        foreach ($attributes as $attribute) {
            $self->add($attribute);
        }

        return $self;
    }

    public function getIterator(): Traversable
    {
        yield from $this->attributes;
    }
}