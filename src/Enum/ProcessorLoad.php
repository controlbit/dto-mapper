<?php
declare(strict_types=1);

namespace ControlBit\Dto\Enum;

/**
 * Enum for possible strategies
 */
enum ProcessorLoad
{
    /**
     * Processor Will Be Executed Before Mapping.
     * Note that regular mapping will still occur, and it may fail if data is corrupted manually.
     */
    case BEFORE_MAPPING;

    /**
     * Processor Will Be Executed After all Mapping is done.
     */
    case AFTER_MAPPING;
}