<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Resources;

use ControlBit\Dto\Contract\ProcessorInterface;

final class IncrementProcessor implements ProcessorInterface
{
    public function process(object|array $source, object $dto): void
    {
        $dto->foo++;
    }
}