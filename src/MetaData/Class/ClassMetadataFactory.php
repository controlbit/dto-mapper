<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Class;

use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\MethodBag;
use ControlBit\Dto\Bag\PropertyBag;
use ControlBit\Dto\MetaData\Method\MethodMetadataFactory;
use ControlBit\Dto\MetaData\Property\PropertyMetadataFactory;
use Psr\Cache\CacheItemPoolInterface;
use function ControlBit\Dto\get_cache_item;
use function ControlBit\Dto\instantiate_attributes;

final readonly class ClassMetadataFactory
{
    public function __construct(
        private PropertyMetadataFactory $propertyMetadataFactory,
        private MethodMetadataFactory   $methodMetadataFactory,
        private ?CacheItemPoolInterface $cache = null,
    ) {
    }

    /**
     * @template T of object
     * @param  class-string<T>|T  $subject
     *
     * @return ClassMetadata<T>
     */
    public function create(object|string $subject): ClassMetadata
    {
        if ($this->cache) {
            $cacheItem = get_cache_item($this->cache, '_cb_dtm_cmtd', $subject);

            if ($cacheItem?->isHit()) {
                return $cacheItem->get();
            }
        }


        $reflection = \is_object($subject) ? new \ReflectionObject($subject) : new \ReflectionClass($subject);

        $properties = new PropertyBag();
        foreach ($reflection->getProperties() as $reflectionProperty) {
            $properties->add($this->propertyMetadataFactory->create($subject, $reflectionProperty->getName()));
        }

        $methods = new MethodBag();
        foreach ($reflection->getMethods() as $reflectionMethod) {
            $methods->add($this->methodMetadataFactory->create($reflection, $reflectionMethod->getName()));
        }

        /** @var class-string<T> $fcqn */
        $fcqn = $reflection->getName();

        $classMetadata = new ClassMetadata(
            $fcqn,
            AttributeBag::fromArray(instantiate_attributes($reflection)),
            $properties,
            $methods
        );

        if ($this->cache) {
            $cacheItem?->set($classMetadata);
        }

        return $classMetadata;
    }
}