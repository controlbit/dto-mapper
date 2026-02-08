<?php
declare(strict_types=1);

namespace ControlBit\Dto\CaseTransformer;

use ControlBit\Dto\Contract\CaseTransformerInterface;

final class ToLowerCaseTransformer implements CaseTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform(array|string $arrayOrString): array|string
    {
        if (\is_string($arrayOrString)) {
            return strtolower($arrayOrString);
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

            $result[\strtolower($key)] = $value;
        }

        return $result;
    }
}