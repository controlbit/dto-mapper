<?php
declare(strict_types=1);

namespace ControlBit\Dto\Transformer;

use ControlBit\Dto\Contract\Transformer\TransformerInterface;
use ControlBit\Dto\Exception\NotImplementedException;
use ControlBit\Dto\Exception\TransformerException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Transforms string values using Symfony Translator.
 * Supports optional value modification before translation via callable or class method.
 */
final class TranslationTransformer implements TransformerInterface
{
    public function __construct(
        protected ?TranslatorInterface $translator = null,
    ) {
    }

    /**
     * Translates a string value using the configured translator.
     *
     * @param  string|null  $value                                                                                             The
     *                                                                                                                         translate
     * {@inheritDoc}
     */
    public function transform(mixed $value, array $options = []): mixed
    {
        if (null === $value) {
            return null;
        }

        return $this->translate($value, $options); // @phpstan-ignore-line
    }

    /**
     * Reverse transformation is not supported for translations.
     *
     * @return never
     * {@inheritDoc}
     */
    public function reverse(mixed $value, array $options = []): mixed
    {
        throw new NotImplementedException('Reverse transformation is not supported for TranslationTransformer.');
    }

    /**
     * Translates a value with optional modification.
     * Returns the original value if translator is not available or value is not a string.
     *
     * @param  array{
     *     domain?: string,
     *     locale?: string,
     *     modifier?: string|array{class-string, string, array<mixed>|null}
     * }  $options
     */
    private function translate(mixed $value, array $options): mixed
    {
        if (!\is_string($value) || null === $this->translator) {
            return $value;
        }

        $modifier = $options['modifier'] ?? null;

        if (\is_string($modifier)) {
            $value = \call_user_func($modifier, $value); // @phpstan-ignore-line
        }

        if (\is_array($modifier)) {
            $args = $modifier[2] ?? [];
            [$class, $method] = $modifier;

            $value = \call_user_func([$class, $method], $value, ...$args); // @phpstan-ignore-line
        }
        
        if(!is_string($value)) {
            throw new TransformerException(
                \sprintf(
                    'Translation modifier must return string, %s given.',
                    \get_debug_type($value)
                )
            );
        }

        return $this->translator->trans(
            $value,
            [],
            $options['domain'] ?? null,
            $options['locale'] ?? null,
        );
    }


}