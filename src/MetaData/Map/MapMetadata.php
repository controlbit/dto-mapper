<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Map;

final class MapMetadata
{
    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function __construct(
        private readonly ?string $sourceMember = null,
        private readonly ?string $sourceMethod = null,
        private readonly ?string $destinationMember = null,
        private readonly ?string $destinationMethod = null,
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
}