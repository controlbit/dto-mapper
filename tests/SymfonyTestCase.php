<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests;

use ControlBit\Dto\Tests\Bridge\Symfony\App\SymfonyTestKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SymfonyTestCase extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return SymfonyTestKernel::class;
    }
}