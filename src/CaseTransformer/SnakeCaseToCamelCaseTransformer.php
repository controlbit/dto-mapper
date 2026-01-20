<?php
declare(strict_types=1);

namespace ControlBit\Dto\CaseTransformer;

use ControlBit\Dto\Contract\CaseTransformerInterface;

/**
 * @psalm-import-type AssociativeArray from \ControlBit\Dto\Contract\CaseTransformerInterface
 */
final class SnakeCaseToCamelCaseTransformer implements CaseTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform(array|string $arrayOrString): array|string
    {
        if (\is_string($arrayOrString)) {
            return $this->snakeToCamel($arrayOrString);
        }

        $result = [];

        /** @var AssociativeArray|string|int $value */
        foreach ($arrayOrString as $key => $value) {
            if (\is_array($value)) {
                $value = $this->transform($value);
            }

            if (!\is_string($key)) {
                $result[$key] = $value;
                continue;
            }

            $result[$this->snakeToCamel($key)] = $value;
        }

        return $result;
    }

    private function snakeToCamel(string $string): string
    {
        return \lcfirst(\str_replace(' ', '', \ucwords(\str_replace('_', ' ', $string))));
    }
}