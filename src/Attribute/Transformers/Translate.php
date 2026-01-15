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
        protected ?string           $locale = null,

        /**
         * The translation domain to use. If null, the default domain will be used.
         */
        protected ?string           $domain = null,

        /**
         * Modifies your value just before it gets translated, so that, if you have prefix,
         * suffix, or case transformation, you can customize the value before translation.
         *
         * Since you can't use closures in attributes, you can write as an array [$this, 'methodName'].
         */
        protected array|string|null $modifier = null,

        /**
         * Do not translate when mapping from DTO to entity.
         */
        protected bool              $ignoreWhenMappingOnEntity = false,

        /**
         * Do not translate when mapping to DTO.
         */
        protected bool              $ignoreWhenMappingOnDto = false,
    ) {
        parent::__construct(TranslationTransformer::class, [
            'locale'                    => $this->locale,
            'domain'                    => $this->domain,
            'modifier'                  => $this->modifier,
            'caseTransform'             => $this->modifier,
            'ignoreWhenMappingOnEntity' => $this->ignoreWhenMappingOnEntity,
            'ignoreWhenMappingOnDto'    => $this->ignoreWhenMappingOnDto,
        ]);
    }
}