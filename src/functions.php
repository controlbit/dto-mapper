<?php
declare(strict_types=1);

namespace ControlBit\Dto;

use Psr\Cache\CacheItemPoolInterface;
use stdClass;
use Symfony\Component\Cache\CacheItem;

/**
 * @template T of object
 * @param  object|\ReflectionProperty|class-string  $subject
 * @param  class-string<T>                          $attributeClass
 *
 * @return T|null
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

    /** @var ?T $instance */
    $instance = false !== $reflectionAttribute ? $reflectionAttribute->newInstance() : null;

    return $instance;
}

/**
 * @template T of object
 * @param  object|\ReflectionProperty|class-string  $subject
 * @param  class-string<T>                          $attributeClass
 */
function has_attribute(mixed $subject, string $attributeClass): bool
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

    return false !== $reflectionAttribute;
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

function get_cache_item(CacheItemPoolInterface $cache, string $prefix, object|string $subject): ?CacheItem
{
    $cacheKey = get_cache_key($prefix, $subject);

    if (false === $cacheKey) {
        return null;
    }

    return $cache->getItem($cacheKey);
}

function get_cache_key(string $prefix, object|string $subject): false|string
{
    if (\is_object($subject) && stdClass::class === \get_class($subject)) {
        $propNames = \array_keys(\get_object_vars($subject));
        return $prefix . '_' . \md5(\implode(',', $propNames));
    }

    if ($subject === stdClass::class) {
        return false;
    }


    return $prefix . '_' . \md5(\is_object($subject)? \get_class($subject) : $subject);
}