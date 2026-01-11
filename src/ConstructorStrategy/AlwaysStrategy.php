<?php
declare(strict_types=1);

namespace ControlBit\Dto\ConstructorStrategy;

use ControlBit\Dto\Accessor\Setter\ConstructorSetter;
use ControlBit\Dto\Attribute\From;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\GetterInterface;
use ControlBit\Dto\Contract\ConstructorStrategyInterface;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\Exception\MissingArgumentException;
use ControlBit\Dto\Exception\MissingConstructorException;
use ControlBit\Dto\Finder\AccessorFinder;
use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\Mapper\ValueConverter;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;
use ControlBit\Dto\MetaData\Map\MapMetadata;
use ControlBit\Dto\Util\TypeTool;
use function ControlBit\Dto\find_attribute;
use function ControlBit\Dto\instantiate_attributes;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * TODO: Refactor to decouple.
 */
final readonly class AlwaysStrategy implements ConstructorStrategyInterface
{
    public const NAME = 'always';

    public function __construct(
        private ValueConverter $valueConverter,
    ) {
    }

    public function validate(
        \ReflectionClass      $destinationReflectionClass,
        MapMetadataCollection $mapMetadata,
    ): void {
        $constructor = $destinationReflectionClass->getConstructor();

        if (null === $constructor) {
            throw new InvalidArgumentException(
                \sprintf('Constructor Strategy is set to Always, but "%s" has no constructor or it\'s private.',
                         $destinationReflectionClass->getName()
                )
            );
        }

        if ($constructor->getNumberOfRequiredParameters() > \count($mapMetadata)) {
            throw new InvalidArgumentException(
                \sprintf(
                    "Not enough members to map as arguments in constructor (Strategy \"%s\".).", $this->getName()
                )
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function create(
        Mapper                $mapper,
        object                $source,
        ClassMetadata         $sourceMetadata,
        MapMetadataCollection $mapMetadata,
        \ReflectionClass      $destinationReflectionClass,
    ): object {
        $constructor = $destinationReflectionClass->getConstructor();

        if (null === $constructor) {
            throw new MissingConstructorException(
                \sprintf('Class "%s" is missing constructor.', $destinationReflectionClass->getName())
            );
        }

        $availableArguments = $constructor->getParameters();
        $argumentsToPass    = [];

        foreach ($availableArguments as $argument) {
            /** @var MapMetadata $destinationMemberMetaData */
            $destinationMemberMetaData = $mapMetadata->getHavingDestinationMember($argument->getName());
            $destinationMemberMetaData->setMappedInConstructor();
            $sourcePropertyMetadata = $sourceMetadata->getProperty($destinationMemberMetaData->getSourceMember());

            // TODO: Handle From and To
            if (null === $sourcePropertyMetadata) {
                $argumentsToPass[] = $this->getArgumentValue($argument);
                continue;
            }

            /** @var GetterInterface $getter */
            $getter = $sourcePropertyMetadata?->getAccessor()->getGetter();
            $setter = new ConstructorSetter(
                new TypeBag(TypeTool::getReflectionTypes($argument)),
                AttributeBag::fromArray(instantiate_attributes($argument)),
            );

            $value = $this->valueConverter->map(
                $mapper,
                $sourceMetadata,
                $getter,
                $setter,
                $getter->get($source),
            );

            $argumentsToPass[] = $this->getArgumentValue($argument, $value);
        }

        return $destinationReflectionClass->newInstanceArgs($argumentsToPass);
    }

    private function getArgumentValue(\ReflectionParameter $argument, mixed $value = null): mixed
    {
        if ($value !== null) {
            return $value;
        }

        if ($argument->allowsNull()) {
            return null;
        }

        if (!$argument->isDefaultValueAvailable()) {
            throw new MissingArgumentException(
                \sprintf(
                    'Tried to put value for argument "%s" via Constructor in class "%s", but no value was provided and no default value is available.',
                    $argument->getName(),
                    $argument->getDeclaringClass()?->getName()
                )
            );
        }

        return $argument->getDefaultValue();
    }

    public function getName(): string
    {
        return self::NAME;
    }
}