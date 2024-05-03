<?php
declare(strict_types=1);

namespace ControlBit\Dto\Exception;

use ControlBit\Dto\Contract\DtoExceptionInterface;

final class InvalidArgumentException extends \Exception implements DtoExceptionInterface
{

}