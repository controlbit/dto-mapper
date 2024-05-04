<?php
declare(strict_types=1);

namespace ControlBit\Dto\Exception;

use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\DtoExceptionInterface;
use ControlBit\Dto\MetaData\ObjectMetadata;
use ControlBit\Dto\MetaData\PropertyMetadata;

final class ValueException extends \RuntimeException implements DtoExceptionInterface
{
    public function __construct(
        PropertyMetadata $sourcePropertyMetaData,
        ObjectMetadata   $sourceMetaData,
        ObjectMetadata   $destinationMetaData,
        SetterInterface  $setter,
        ?\Throwable      $previous = null,
    ) {
        $message = \sprintf(
            'Cannot map value "%s" of "%s" to destination object of "%s". Check if writable/callable and correct type(s) ([%s] to [%s])',
            $sourcePropertyMetaData->getName(),
            $sourceMetaData->getFcqn(),
            $destinationMetaData->getFcqn(),
            implode(',', $sourcePropertyMetaData->getType()->all()),
            implode(',', $setter->getType()->all())
        );

        parent::__construct($message, 0, $previous);
    }
}