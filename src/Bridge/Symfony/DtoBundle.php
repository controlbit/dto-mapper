<?php

declare(strict_types=1);

namespace ControlBit\Dto\Bridge\Symfony;

use ControlBit\Dto\Bridge\Symfony\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DtoBundle extends Bundle
{
    public function getNamespace(): string
    {
        return 'dto_bundle';
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new Extension();
    }
}