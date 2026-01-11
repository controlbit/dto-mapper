<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute;

/**
 * Used on property when we want to map property to another property.
 * Good in case when we have some backward compatibility or so.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
readonly final class To
{
    public function __construct(
        private ?string $member = null,
        private ?string $setter = null,
    )
    {
    }

    public function getMember(): ?string
    {
        return $this->member;
    }

    public function getSetter(): ?string
    {
        return $this->setter;
    }
}