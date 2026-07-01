<?php
declare(strict_types=1);

namespace ControlBit\Dto\Processor;

use ControlBit\Dto\Attribute\Processor as ProcessorAttribute;
use ControlBit\Dto\Contract\ProcessorInterface;
use ControlBit\Dto\Enum\ProcessorLoad;
use ControlBit\Dto\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use function ControlBit\Dto\find_attribute;

final readonly class Processor
{
    public function __construct(private ?ContainerInterface $container = null)
    {
    }

    /**
     * @param  object|array<string,mixed>  $source  The source data - either an object or associative array
     */
    public function process(array|object $source, object $destination, ProcessorLoad $load): void
    {
        /** @var ProcessorAttribute|null $attribute */
        $attribute        = find_attribute($destination, ProcessorAttribute::class);

        if (null === $attribute || $attribute->getLoad() !== $load) {
            return;
        }

        $processor = $this->instantiateProcessor($attribute->getProcessorClass());

        if (\is_object($source)) {
            $source = clone $source;
        }

        $processor->process($source, $destination);
    }

    private function instantiateProcessor(string $classOrId): ProcessorInterface
    {
        if (null !== $this->container && $this->container->has($classOrId)) {
            /** @var object|null $processorService */
            $processorService = $this->container->get($classOrId);
        }

        if (isset($processorService)) {
            $this->validateProcessor($processorService);

            /** @var ProcessorInterface $processorService */
            return $processorService;
        }

        $this->validateProcessor($classOrId);

        /* @phpstan-ignore-next-line */
        $processor = (new \ReflectionClass($classOrId))->newInstanceWithoutConstructor();

        /** @var ProcessorInterface $processor */
        return $processor;
    }

    private function validateProcessor(string|object $processorClassOrObject): void
    {
        if (\is_string($processorClassOrObject) && !\class_exists($processorClassOrObject)) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Dto Processor class "%s" does not exists.',
                    $processorClassOrObject,
                )
            );
        }

        if (\is_a($processorClassOrObject, ProcessorInterface::class, true)) {
            return;
        }

        throw new InvalidArgumentException(
            \sprintf(
                'Dto Processor "%s" must implement "%s".',
                \is_object($processorClassOrObject) ? $processorClassOrObject::class : $processorClassOrObject,
                ProcessorInterface::class,
            )
        );
    }
}