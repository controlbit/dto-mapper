<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute\Transformers;

use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Transformer\TranslationTransformer;

/**
 * Tells how this value should be translated.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class Translate extends Transformer
{
    public function __construct(
        /**
         * The locale to use for translation. If null, the default locale will be used.
         */
        protected readonly ?string    $locale = null,

        /**
         * The translation domain to use. If null, the default domain will be used.
         */
        protected readonly ?string    $domain = null,

        /**
         * The prefix to prepend to translation keys. If null, no prefix will be used.
         */
        protected readonly ?string    $prefix = null,

        /**
         * Do not translate when mapping from DTO to entity.
         */
        protected bool                $ignoreWhenMappingOnEntity = false,

        /**
         * Do not translate when mapping to DTO.
         */
        protected bool                $ignoreWhenMappingOnDto = false,
    ) {
        parent::__construct(TranslationTransformer::class, [
            'locale'                    => $this->locale,
            'domain'                    => $this->domain,
            'prefix'                    => $this->prefix,
            'ignoreWhenMappingOnEntity' => $this->ignoreWhenMappingOnEntity,
            'ignoreWhenMappingOnDto'    => $this->ignoreWhenMappingOnDto,
        ]);
    }
}