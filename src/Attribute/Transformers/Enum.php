<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute\Transformers;

use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Transformer\EnumTransformer;
use ControlBit\Dto\Transformer\TranslationTransformer;

/**
 * Transforms to and from Enum
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class Enum extends Transformer
{
    public function __construct(
        /**
         * class-string<\BackedEnum>
         */
        string $class,
        bool   $toEnum = false,
        bool   $fromEnum = false,
        bool   $failWhenInvalidEnumValue = false,
    ) {
        parent::__construct(EnumTransformer::class, [
            'class'                    => $class,
            'toEnum'                   => $toEnum,
            'fromEnum'                 => $fromEnum,
            'failWhenInvalidEnumValue' => $failWhenInvalidEnumValue,
        ]);
    }
}