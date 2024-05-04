<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests;

use ControlBit\Dto\Factory;
use ControlBit\Dto\Mapper\Mapper;
use PHPUnit\Framework\TestCase;

class LibraryTestCase extends TestCase
{
    protected function getMapper(): Mapper
    {
        return Factory::create();
    }
}