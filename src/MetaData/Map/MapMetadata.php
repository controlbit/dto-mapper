<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Map;

use ControlBit\Dto\Contract\Transformer\TransformableInterface;

final class MapMetadata implements TransformableInterface
{
    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function __construct(
        private readonly ?string $sourceMember = null,
        private readonly ?string $sourceMethod = null,
        private readonly ?string $destinationMember = null,
        private readonly ?string $destinationMethod = null,
        private readonly ?string $sourceTransformer = null,
        private readonly ?string $destinationTransformer = null,
        private bool             $mappedInConstructor = false,
    ) {
    }

    public function getSourceMember(): ?string
    {
        return $this->sourceMember;
    }

    public function getDestinationMember(): ?string
    {
        return $this->destinationMember;
    }

    /**
     * {@inheritDoc}
     */
    public function getTransformerClassOrId(): ?string
    {
        return $this->transformer;
    }

    public function hasTransformer(): bool
    {
        return $this->transformer !== null;
    }

    public function setMappedInConstructor(): self
    {
        $this->mappedInConstructor = true;

        return $this;
    }

    public function isMappedInConstructor(): bool
    {
        return $this->mappedInConstructor;
    }

    public function getDestinationMethod(): ?string
    {
        return $this->destinationMethod;
    }

    public function getSourceMethod(): ?string
    {
        return $this->sourceMethod;
    }

    public function getSourceTransformer(): ?string
    {
        return $this->sourceTransformer;
    }

    public function getDestinationTransformer(): ?string
    {
        return $this->destinationTransformer;
    }
}