<?php
declare(strict_types=1);

namespace ControlBit\Dto\Transformer;

use ControlBit\Dto\Contract\Transformer\TransformerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TranslationTransformer implements TransformerInterface
{
    public function __construct(
        protected ?TranslatorInterface $translator = null,
    ) {
    }

    /**
     * @param  string|null  $value
     * {@inheritDoc}
     */
    public function transform(mixed $value, array $options = []): mixed
    {
        if (null === $value) {
            return null;
        }

        if ($options['ignoreWhenMappingOnEntity']) {
            return $value;
        }

        return $this->translate($value, $options);
    }

    /**
     * @param  string|null  $value
     * {@inheritDoc}
     */
    public function reverse(mixed $value, array $options = []): mixed
    {
        if (null === $value) {
            return null;
        }

        if ($options['ignoreWhenMappingOnDto']) {
            return $value;
        }

        return $this->translate($value, $options);
    }

    private function translate(mixed $value, array $options): mixed
    {
        if (!\is_string($value) || null === $this->translator) {
            return $value;
        }

        $prefix = $options['prefix'];

        $translationKey = $prefix !== null ? $prefix.$value : $value;

        return $this->translator->trans(
            $translationKey,
            [],
            $prefix['domain'] ?? null,
            $prefix['locale'] ?? null,
        );
    }


}