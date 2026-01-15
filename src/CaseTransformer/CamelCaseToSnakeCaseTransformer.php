<?php
declare(strict_types=1);

namespace ControlBit\Dto\CaseTransformer;

use ControlBit\Dto\Contract\CaseTransformerInterface;

final class CamelCaseToSnakeCaseTransformer implements CaseTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform(array|string $arrayOrString): array|string
    {
        if (\is_string($arrayOrString)) {
            return $this->camelToSnake($arrayOrString);
        }

        $result = [];
        foreach ($arrayOrString as $key => $value) {
            if (\is_array($value)) {
                $value = $this->transform($value);
            }

            if (!\is_string($key)) {
                $result[$key] = $value;
                continue;
            }

            $result[$this->camelToSnake($key)] = $value;
        }

        return $result;
    }

    private function camelToSnake(string $string): string
    {
        return \strtolower(\preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }
}