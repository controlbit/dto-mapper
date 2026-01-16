<?php
declare(strict_types=1);

namespace ControlBit\Dto\Mapper;

use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\Attribute\From;
use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Contract\Accessor\GetterInterface;
use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\ValueConverterInterface;
use ControlBit\Dto\Contract\Transformer\TransformableInterface;
use ControlBit\Dto\Contract\Transformer\TransformerInterface;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class ValueConverter
{
    /**
     * @param  iterable<ValueConverterInterface>  $valueConverters
     */
    public function __construct(
        private iterable            $valueConverters,
        private ?ContainerInterface $container = null,
    ) {
    }

    public function map(
        Mapper          $mapper,
        ClassMetadata   $sourceMetadata,
        GetterInterface $getter,
        SetterInterface $setter,
        mixed           $value,
    ): mixed {
        $isSourceTransformerOnly = $this->shouldFollowSourceTransformerOnly($sourceMetadata, $getter);

        if ($getter instanceof TransformableInterface && $isSourceTransformerOnly) {
            $value = $this->transform($value, $sourceMetadata, $getter, true);
        }

        if ($setter instanceof TransformableInterface && !$isSourceTransformerOnly) {
            $value = $this->transform($value, $sourceMetadata, $setter, false);
        }

        foreach ($this->valueConverters as $valueConverter) {
            if (!$valueConverter->supports($setter, $value)) {
                continue;
            }

            $value = $valueConverter->execute($mapper, $setter, $value);
        }

        return $value;
    }

    private function transform(
        mixed                  $value,
        ClassMetadata          $sourceMetadata,
        TransformableInterface $transformable,
        bool                   $isSourceTransformerOnly,
    ): mixed {
        if (!$transformable->hasTransformersAttributes()) {
            return $value;
        }

        $transformerAttributes = $transformable->getTransformerAttributes();

        foreach ($transformerAttributes as $attribute) {
            $classOrId           = $attribute->getTransformerIdOrClass();
            $options             = $attribute->getOptions();
            $transformerInstance = $this->instantiateTransformer($classOrId); // @phpstan-ignore-line
            $isReverseTransform  = $this->shouldReverseTransform($attribute, $sourceMetadata, $isSourceTransformerOnly);

            $value = $isReverseTransform
                ? $transformerInstance->reverse($value, $options)
                : $transformerInstance->transform($value, $options);
        }

        return $value;
    }

    /**
     * @param  string|class-string  $classOrId
     */
    private function instantiateTransformer(string $classOrId): TransformerInterface
    {
        if (null !== $this->container && $this->container->has($classOrId)) {
            $transformerService = $this->container->get($classOrId);
        }

        if (isset($transformerService)) {
            $this->validateTransformer($transformerService);

            /** @var TransformerInterface $transformerService */
            return $transformerService;
        }

        $this->validateTransformer($classOrId);

        /* @phpstan-ignore-next-line */
        return (new \ReflectionClass($classOrId))->newInstanceWithoutConstructor();
    }

    private function validateTransformer(object|string $transformerClassOrObject): void
    {
        if (\is_string($transformerClassOrObject) && !\class_exists($transformerClassOrObject)) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Transformer class "%s" does not exists.',
                    $transformerClassOrObject,
                )
            );
        }

        if (\is_a($transformerClassOrObject, TransformerInterface::class, true)) {
            return;
        }

        throw new InvalidArgumentException(
            \sprintf(
                'Transformer "%s" must implement "%s".',
                \is_object($transformerClassOrObject) ? $transformerClassOrObject::class : $transformerClassOrObject,
                TransformerInterface::class,
            )
        );
    }

    private function shouldFollowSourceTransformerOnly(
        ClassMetadata   $sourceMetadata,
        GetterInterface $getter,
    ): bool {
        if (!$sourceMetadata->getAttributes()->has(Entity::class)) {
            return false;
        }

        if ($sourceMetadata->getAttributes()->has(Dto::class)) {
            if (null !== $sourceMetadata->getAttributes()->get(Dto::class)?->getEntityClass()) {
                return true;
            }
        }

        return $getter->getAttributes()->has(From::class);
    }

    private function shouldReverseTransform(
        Transformer   $transformerAttribute,
        ClassMetadata $sourceMetadata,
        bool          $isSourceAttributesOnly,
    ): bool {
        $reverseOption = $transformerAttribute->getOptions()['reverse'] ?? null;

        if (true === $reverseOption) {
            return true;
        }

        if (false === $reverseOption) {
            return false;
        }

        if (!$isSourceAttributesOnly) {
            return false;
        }

        if ($sourceMetadata->getFqcn() !== \stdClass::class) {
            return false;
        }

        return true;
    }
}