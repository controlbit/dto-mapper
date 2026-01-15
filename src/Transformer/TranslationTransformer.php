<?php
declare(strict_types=1);

namespace ControlBit\Dto\Transformer;

use ControlBit\Dto\Contract\Transformer\TransformerInterface;
use ControlBit\Dto\Exception\NotImplementedException;
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
       throw new NotImplementedException('Reverse transformation is not supported for TranslationTransformer.');
    }

    private function translate(mixed $value, array $options): mixed
    {
        if (!\is_string($value) || null === $this->translator) {
            return $value;
        }

        $modifier = $options['modifier'] ?? null;

        if (\is_string($modifier)) {
            $value = \call_user_func($modifier, $value);
        }

        if (\is_array($modifier)) {
            [$class, $method] = $modifier;
            $value = \call_user_func([$class, $method], $value);
        }

        return $this->translator->trans(
            $value,
            [],
            $options['domain'] ?? null,
            $options['locale'] ?? null,
        );
    }


}