<?php
declare(strict_types=1);

namespace ControlBit\Dto\Exception;

use ControlBit\Dto\Contract\Accessor\GetterInterface;
use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\DtoExceptionInterface;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadata;

class PropertyMapException extends \RuntimeException implements DtoExceptionInterface
{
    public function __construct(
        MapMetadata     $mapMetadata,
        ClassMetadata   $sourceMetaData,
        ClassMetadata   $destinationMetaData,
        SetterInterface $setter,
        GetterInterface $getter,
        ?\Throwable     $previous = null,
    ) {
        $message = \sprintf(
            'Cannot map property "%s" of "%s" to destination object of "%s". Check if writable/callable and correct type(s) ([%s] to [%s]).',
            $mapMetadata->getSourceMember(),
            $sourceMetaData->getFqcn(),
            $destinationMetaData->getFqcn(),
            $getter->getType(),
            $setter->getType(),
        );

        parent::__construct($message, 0, $previous);
    }
}