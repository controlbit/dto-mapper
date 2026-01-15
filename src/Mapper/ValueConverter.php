<?php
declare(strict_types=1);

namespace ControlBit\Dto\Mapper;

use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\Attribute\From;
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
            $isReverse = $this->shouldReverseTransform($getter, $sourceMetadata, true);
            $value = $this->transform($value, $getter, $isReverse);
        }

        if ($setter instanceof TransformableInterface && !$isSourceTransformerOnly) {
            $isReverse = $this->shouldReverseTransform($setter, $sourceMetadata, false);
            $value = $this->transform($value, $setter, $isReverse);
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
        TransformableInterface $transformable,
        bool                   $isReverseTransform,
    ): mixed {
        if (!$transformable->hasTransformer()) {
            return $value;
        }

        $classOrId            = $transformable->getClassOrId();
        $options              = $transformable->getOptions();
        $transformer          = $this->instantiateTransformer($classOrId); // @phpstan-ignore-line

        if ($isReverseTransform) {
            return $transformer->reverse($value, $options);
        }

        return $transformer->transform($value, $options);
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
        TransformableInterface $transformable,
        ClassMetadata          $sourceMetadata,
        bool                   $isSourceAttributesOnly,
    ): bool {
        $reverseOption = $transformable->getOptions()['reverse'] ?? null;

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