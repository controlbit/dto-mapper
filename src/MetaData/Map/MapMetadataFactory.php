<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Map;

use ControlBit\Dto\Attribute\From;
use ControlBit\Dto\Attribute\To;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Property\PropertyMetadata;
use Psr\Cache\CacheItemPoolInterface;
use function ControlBit\Dto\get_cache_item;
use function ControlBit\Dto\get_cache_key;

final class MapMetadataFactory
{
    public function __construct(private ?CacheItemPoolInterface $cache = null)
    {
    }

    /**
     * @template S of object
     * @template D of object
     *
     * @param  ClassMetadata<S>  $sourceMetadata
     * @param  ClassMetadata<D>  $destinationMetadata
     */
    public function create(object $source, ClassMetadata $sourceMetadata, ClassMetadata $destinationMetadata): MapMetadataCollection
    {
        if ($this->cache) {
            $sourceKey             = get_cache_key('source', $source);
            $destinationKey        = get_cache_key('destination', $destinationMetadata->getFqcn());
            $validCompoundCacheKey = false !== $sourceKey && false !== $destinationKey;
            $cacheItem             = $validCompoundCacheKey
                ? get_cache_item($this->cache, '_cb_dtm_mmtd', $sourceKey.'_'.$destinationKey)
                : null;

            if ($cacheItem?->isHit()) {
                return $cacheItem->get();
            }
        }

        $mapMetadata               = new MapMetadataCollection();
        $visitedDestinationMembers = [];

        foreach ($sourceMetadata->getProperties() as $propertyMetadata) {
            $attributes = $propertyMetadata->getAttributes();

            if (!$attributes->has(To::class)) {
                continue;
            }

            $to     = $attributes->get(To::class);
            $member = $to?->getMember();

            if (\in_array($member, $visitedDestinationMembers, true)) {
                continue;
            }

            $visitedDestinationMembers[] = $member;
            $mapMetadata->add($this->mapTo($propertyMetadata, $attributes));
        }

        foreach ($destinationMetadata->getProperties() as $propertyMetadata) {
            $attributes = $propertyMetadata->getAttributes();
            $member     = $propertyMetadata->getName();

            if (\in_array($member, $visitedDestinationMembers, true)) {
                continue;
            }

            $mapMetadata->add(match (true) {
                $attributes->has(From::class) => $this->mapFrom($propertyMetadata, $attributes),
                default                       => new MapMetadata(
                    $member,
                    null,
                    $member,
                    null,
                    $propertyMetadata->hasIgnoreAttribute()
                ),
            });
        }

        if ($this->cache && isset($cacheItem)) {
            $cacheItem?->set($mapMetadata);
            $this->cache->save($cacheItem);
        }

        return $mapMetadata;
    }

    private function mapTo(PropertyMetadata $propertyMetadata, AttributeBag $attributes): MapMetadata
    {
        /** @var To $attribute */
        $attribute = $attributes->get(To::class);

        return new MapMetadata(
            $propertyMetadata->getName(),
            null,
            $attribute->getMember(),
            $attribute->getSetter(),
            $propertyMetadata->hasIgnoreAttribute(),
        );
    }

    private function mapFrom(PropertyMetadata $propertyMetadata, AttributeBag $attributes): MapMetadata
    {
        /** @var From $attribute */
        $attribute = $attributes->get(From::class);

        return new MapMetadata(
            $attribute->getMember(),
            $attribute->getGetter(),
            $propertyMetadata->getName(),
            null,
            $propertyMetadata->hasIgnoreAttribute(),
        );
    }
}