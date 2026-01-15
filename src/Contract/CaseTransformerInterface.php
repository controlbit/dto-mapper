<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract;

interface CaseTransformerInterface
{
    /**
     * @param  array<string, mixed>|string  $arrayOrString
     *
     * @return array<string, mixed>|string
     */
    public function transform(array|string $arrayOrString): array|string;
}