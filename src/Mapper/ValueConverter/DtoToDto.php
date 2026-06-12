<?php
declare(strict_types=1);

namespace ControlBit\Dto\Mapper\ValueConverter;

use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\MapperInterface;
use ControlBit\Dto\Contract\Mapper\ValueConverterInterface;
use ControlBit\Dto\Mapper\Mapper;
use function ControlBit\Dto\find_attribute;

/**
 * Case when we have some object on the source property side, and we need to convert it/map it to DTO.
 */
final class DtoToDto implements ValueConverterInterface
{
    public function supports(SetterInterface $setter, mixed $value): bool
    {
        if (!\is_object($value)) {
            return false;
        }

        if (!$setter->getType()->hasOnlyOneClass()) {
            return false;
        }

        $class = $setter->getType()->getOneClass();

        if (null !== find_attribute($class, Dto::class)) {
            return true;
        }

        if ($setter->getAttributes()->has(Dto::class)) {
            return true;
        }

        return false;
    }

    /**
     * @param  array<mixed>  $value
     */
    public function execute(Mapper $mapper, SetterInterface $setter, mixed $value): mixed
    {
        return $mapper->map($value, $setter->getType()->getOneClass()); // @phpstan-ignore-line
    }
}