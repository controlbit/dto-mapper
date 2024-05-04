<?php
declare(strict_types=1);

namespace ControlBit\Dto\Bag;

final readonly class TypeBag
{
    /**
     * @param  array<string|null>  $types
     */
    public function __construct(private array $types)
    {
    }

    public function has(string $type): bool
    {
        return \in_array($type, $this->types, true);
    }

    public function hasNull(): bool
    {
        return \in_array(null, $this->types, true);
    }

    public function hasIterable(): bool
    {
        return
            \in_array('array', $this->types, true)
            ||
            \in_array('iterable', $this->types, true);
    }

    /**
     * @param  class-string|null  $class
     */
    public function hasOneClass(?string $class = null): bool
    {
        $classes = \array_filter(
            $this->types,
            static fn(?string $type): bool => null !== $type && \class_exists($type),
        );

        if (\count($classes) !== 1) {
            return false;
        }

        if (null === $class) {
            return true;
        }

        return $classes[0] === $class;
    }

    /**
     * @return class-string|null
     */
    public function getOneClass(): ?string
    {
        $classes = \array_filter(
            $this->types,
            static fn(?string $type): bool => null !== $type && \class_exists($type),
        );

        return $classes[0] ?? null;
    }

    /**
     * @return array<string|null>
     */
    public function all(): array
    {
        return $this->types;
    }
}