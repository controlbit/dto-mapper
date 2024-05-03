<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract\Mapper;

interface CaseTransformerInterface
{
    /**
     * @param  array<string, mixed>  $array
     *
     * @return array<string, mixed>
     */
    public function transform(array $array): array;
}