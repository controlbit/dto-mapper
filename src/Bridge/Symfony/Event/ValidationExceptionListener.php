<?php
declare(strict_types=1);

namespace ControlBit\Dto\Bridge\Symfony\Event;

use ControlBit\Dto\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\ConstraintViolationInterface;

final class ValidationExceptionListener
{
    private bool $throwJsonBadRequest;

    public function __construct(bool $throwJsonBadRequest)
    {
        $this->throwJsonBadRequest = $throwJsonBadRequest;
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof ValidationException || $this->throwJsonBadRequest !== true) {
            return;
        }

        $request     = $event->getRequest();
        $contentType = $request->headers->get('content-type');
        $format      = $request->attributes->get('_route_params')['_format'] ?? null; // @phpstan-ignore-line

        if ($format !== 'json' && \strtolower((string)$contentType) !== 'application/json') {
            return;
        }

        // sends the modified response object to the event
        $event->setResponse($this->getJsonResponse($exception));
    }

    private function getJsonResponse(ValidationException $exception): JsonResponse
    {
        $errors = \array_map(static function (ConstraintViolationInterface $violation) {
            return [
                'message'       => $violation->getMessage(),
                'path'          => $violation->getPropertyPath(),
                'invalid_value' => $violation->getInvalidValue(),
                'template'      => $violation->getMessageTemplate(),
            ];
        }, [...$exception->getViolationList()]);

        return new JsonResponse(
            [
                'errors' => $errors,
            ],
            Response::HTTP_BAD_REQUEST,
        );
    }
}