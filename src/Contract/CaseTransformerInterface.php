<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract;

/**
 * @psalm-type AssociativeArray = array<int|string, mixed>
 */
interface CaseTransformerInterface
{
    /**
     * Transforms the case of array keys or string.
     *
     * @template T of AssociativeArray|string
     * @param  T  $arrayOrString
     *
     * @return (T is string ? string : AssociativeArray)
     */
    public function transform(array|string $arrayOrString): array|string;
}