<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Resources;

use Symfony\Component\Validator\Constraints as Assert;

final class AssertedDto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    public ?string $foo = null;
}