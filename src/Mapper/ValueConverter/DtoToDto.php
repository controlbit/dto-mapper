<?php
declare(strict_types=1);

namespace ControlBit\Dto\Mapper\ValueConverter;

use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\MapperInterface;
use ControlBit\Dto\Contract\Mapper\ValueConverterInterface;
use ControlBit\Dto\Mapper\Mapper;

/**
 * Case when we have some object on the source property side, and we need to convert it/map it to DTO.
 */
final class DtoToDto implements ValueConverterInterface
{
    public function __construct(private readonly MapperInterface $mapper)
    {
    }

    public function supports(SetterInterface $setter, mixed $value): bool
    {
        if (!\is_object($value)) {
            return false;
        }

        if (!$setter->getType()->hasOnlyOneClass()) {
            return false;
        }

        if (!$setter->getAttributes()->has(Dto::class)) {
            return false;
        }

        return true;
    }

    /**
     * @param  array<mixed>  $value
     */
    public function execute(Mapper $mapper, SetterInterface $setter, mixed $value): mixed
    {
        return $mapper->map($value, $setter->getType()->getOneClass());
    }
}