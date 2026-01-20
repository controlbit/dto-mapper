<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute;

use ControlBit\Dto\Contract\Transformer\TransformerInterface;
use ControlBit\Dto\Exception\InvalidArgumentException;

/**
 * Defines a transformer on property.
 * Transformer will be called before value is
 * applied to destination property.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class Transformer
{
    /**
     * @var class-string<TransformerInterface>
     */
    private string $transformerClass;
    /**
     * @var array<string, mixed>
     */
    private array $options;

    /**
     * @param  class-string          $transformerClass
     * @param  array<string, mixed>  $options
     */
    public function __construct(string $transformerClass, array $options = [])
    {
        if (!\class_exists($transformerClass)) {
            throw new InvalidArgumentException(\sprintf('Transformer Class "%s" does not exist.', $transformerClass));
        }

        if (!is_a($transformerClass, TransformerInterface::class, true)) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Class "%s" does not transformer. It must implement "%s".',
                    $transformerClass,
                    TransformerInterface::class
                )
            );
        }

        $this->transformerClass = $transformerClass;
        $this->options          = $options;
    }

    /**
     * @return class-string<TransformerInterface>
     */
    public function getTransformerIdOrClass(): string
    {
        return $this->transformerClass;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}