<?php
declare(strict_types=1);

namespace ControlBit\Dto\Builder;

use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\Exception\ValueException;
use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\Mapper\ValueConverter;
use ControlBit\Dto\MetaData\ObjectMetadata;
use ControlBit\Dto\Util\Initializer;

final class Builder
{
    private ?ObjectMetadata $sourceMetadata      = null;
    private ?ObjectMetadata $destinationMetadata = null;

    public function __construct(private readonly ValueConverter $valueConverter)
    {
    }

    public function withSourceMetadata(ObjectMetadata $sourceMetadata): Builder
    {
        $new                 = clone $this;
        $new->sourceMetadata = $sourceMetadata;

        return $new;
    }

    public function withDestinationMetadata(ObjectMetadata $destinationMetadata): Builder
    {
        $new                      = clone $this;
        $new->destinationMetadata = $destinationMetadata;

        return $new;
    }

    public function build(object $source, object $destination, Mapper $mapper): object
    {
        $this->validateBuild();

        foreach ($this->sourceMetadata->getProperties() as $propertyMetaData) { // @phpstan-ignore-line
            if ($propertyMetaData->shouldIgnore()) {
                continue;
            }

            /** @var SetterInterface $setter */
            $setter           = $propertyMetaData->getDestinationSetter();
            $destinationValue = $this->valueConverter->map(
                $mapper,
                $source,
                $this->sourceMetadata, // @phpstan-ignore-line (cannot be null)
                $propertyMetaData->setDestinationSetter($setter),
                $setter,
            );

            try {
                $setter->set($destination, $destinationValue);
            } catch (\Throwable $e) {
                throw new ValueException(
                    $propertyMetaData,
                    $this->sourceMetadata,      // @phpstan-ignore-line (is not null at this point)
                    $this->destinationMetadata, // @phpstan-ignore-line (is not null at this point)
                    $setter,
                    $e,
                );
            }
        }

        Initializer::autoInitialize($destination, $this->destinationMetadata); // @phpstan-ignore-line

        return $destination;
    }

    private function validateBuild(): void
    {
        if (null === $this->sourceMetadata) {
            throw new InvalidArgumentException('Build requires Source metadata to be set.');
        }

        if (null === $this->destinationMetadata) {
            throw new InvalidArgumentException('Build requires Source metadata to be set.');
        }
    }
}