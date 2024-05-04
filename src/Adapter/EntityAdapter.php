<?php
declare(strict_types=1);

namespace ControlBit\Dto\Adapter;

use ControlBit\Dto\Attribute\Entity as EntityAttribute;
use ControlBit\Dto\Attribute\Identifier;
use ControlBit\Dto\Contract\Mapper\MapAdapterInterface;
use ControlBit\Dto\Exception\RuntimeException;
use Doctrine\Persistence\ManagerRegistry;
use function ControlBit\Dto\find_attribute;

/**
 * Adapts Source and Destination
 * when Source is DTO Object and destination is Given Doctrine Entity object or an Entity class,
 * using #[Entity
 */
final class EntityAdapter implements MapAdapterInterface
{
    public function __construct(private readonly ManagerRegistry $doctrineRegistry)
    {
    }

    /**
     * @param  object  $source
     */
    public function adapt(mixed &$source, mixed &$destination): bool
    {
        if (!$this->supports($source, $destination)) {
            return false;
        }

        /** @var EntityAttribute $entityAttribute */
        $entityAttribute = find_attribute($source, EntityAttribute::class);
        $entityClass     = $entityAttribute->getTarget();
        $identifier      = $this->findIdentifier($source);

        if (null !== $identifier) {
            $existingEntity = $this->fetchEntity($identifier, $entityClass);
        }

        $destination = $existingEntity ?? (new \ReflectionClass($entityClass))->newInstanceWithoutConstructor();

        return true;
    }

    private function supports(mixed $source, mixed $destination): bool
    {
        if (!\is_object($source)) {
            return false;
        }

        if (null === find_attribute($source, EntityAttribute::class)) {
            return false;
        }

        if (null !== $destination) {
            return false;
        }

        return true;
    }

    private function findIdentifier(object $source): string|int|null
    {
        $reflection = new \ReflectionClass($source);

        foreach ($reflection->getProperties() as $property) {
            $attribute = find_attribute($property, Identifier::class);

            if (null === $attribute) {
                continue;
            }

            // @phpstan-ignore-next-line
            return $property->isInitialized($source) ? $property->getValue($source) : null;
        }

        return null;
    }

    /**
     * @template T of object
     * @param  string|int       $identifier
     * @param  class-string<T>  $entityClass
     */
    private function fetchEntity(string|int $identifier, string $entityClass): ?object
    {
        $manager = $this->doctrineRegistry->getManagerForClass($entityClass);

        if (null === $manager) {
            throw new RuntimeException(
                \sprintf('Entity manager not found for entity "%s".', $entityClass)
            );
        }

        return $manager->find($entityClass, $identifier);
    }
}