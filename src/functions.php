<?php
declare(strict_types=1);

namespace ControlBit\Dto;

/**
 * @template T of object
 * @param  object|\ReflectionProperty|class-string  $subject
 * @param  class-string<T>                          $attributeClass
 *
 * @return object|null
 */
function find_attribute(mixed $subject, string $attributeClass): ?object
{
    if (\is_string($subject)) {
        $subject = new \ReflectionClass($subject);
    }

    if (!$subject instanceof \Reflector) {
        $subject = new \ReflectionObject($subject);
    }

    /** @var \ReflectionAttribute[] $dtoPropAttributes */
    /** @var \ReflectionObject $subject */
    $dtoPropAttributes = $subject->getAttributes();

    $reflectionAttribute = current(
        array_filter($dtoPropAttributes, static fn($attribute) => $attribute->getName() === $attributeClass)
    );

    /** @var ?object $instance */
    $instance = false !== $reflectionAttribute ? $reflectionAttribute->newInstance() : null;

    return $instance;
}

/**
 * @template T of object
 * @param  \ReflectionProperty|\ReflectionMethod|\ReflectionObject|\ReflectionClass<T>|\ReflectionParameter  $reflection
 *
 * @return object[]
 */
function instantiate_attributes(
    \ReflectionProperty|\ReflectionMethod|\ReflectionObject|\ReflectionClass|\ReflectionParameter $reflection,
): array {
    return \array_map(static function (\ReflectionAttribute $reflectionAttribute) {
        return $reflectionAttribute->newInstance();
    }, $reflection->getAttributes());
}