<?php
declare(strict_types=1);

namespace ControlBit\Dto\Bridge\Symfony\Event;

use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\Exception\ValidationException;
use ControlBit\Dto\Mapper\Mapper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function ControlBit\Dto\find_attribute;

class DtoArgumentResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly Mapper              $mapper,
        private readonly ?ValidatorInterface $validator,
    ) {
    }

    /**
     * @return iterable<object>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->shouldResolve($argument)) {
            return [];
        }

        $dto = $this->mapper->map($request, $argument->getType());

        if (false === $dto) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Cannot resolve controller argument "%s" of type "%s" from request.',
                    $argument->getName(),
                    $argument->getType(),
                )
            );
        }

        $this->validate($dto);

        return [$dto];
    }

    private function shouldResolve(ArgumentMetadata $argument): bool
    {
        $type = $argument->getType();

        if (null === $type || !\class_exists($type)) {
            return false;
        }

        if (null === find_attribute($type, Dto::class) && \count($argument->getAttributesOfType(Dto::class)) === 0) {
            return false;
        }

        return true;
    }

    private function validate(object $dto): void
    {
        if (null === $this->validator) {
            return;
        }

        $violations = $this->validator->validate($dto);

        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }
    }
}