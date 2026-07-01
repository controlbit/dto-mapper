<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute;

use ControlBit\Dto\Contract\ProcessorInterface;
use ControlBit\Dto\Enum\ProcessorLoad;
use ControlBit\Dto\Exception\InvalidArgumentException;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
readonly class Processor
{
    /**
     * @var class-string<ProcessorInterface>
     */
    private string $processorClass;

    private ProcessorLoad $load;

    /**
     * @param  class-string  $processorClass
     */
    public function __construct(string $processorClass, ProcessorLoad $load = ProcessorLoad::AFTER_MAPPING,)
    {
        if (!\class_exists($processorClass)) {
            throw new InvalidArgumentException(\sprintf('Processor Class "%s" does not exist.', $processorClass));
        }

        if (!is_a($processorClass, ProcessorInterface::class, true)) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Class "%s" does not an dto processor. It must implement "%s".',
                    $processorClass,
                    ProcessorInterface::class
                )
            );
        }

        $this->processorClass = $processorClass;
        $this->load           = $load;
    }

    /**
     * @return class-string<ProcessorInterface>
     */
    public function getProcessorClass(): string
    {
        return $this->processorClass;
    }

    public function getLoad(): ProcessorLoad
    {
        return $this->load;
    }
}