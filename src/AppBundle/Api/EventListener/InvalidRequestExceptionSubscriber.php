<?php

namespace AppBundle\Api\EventListener;

use AppBundle\Api\Exception\InvalidRequestException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolation;

class InvalidRequestExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onInvalidRequestException',
        ];
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onInvalidRequestException(GetResponseForExceptionEvent $event): void
    {
        /** @var InvalidRequestException $exception */
        $exception = $event->getException();

        if (false === $exception instanceof InvalidRequestException) {
            return;
        }

        $response = new JsonResponse([
            'message' => 'Invalid request.',
            'errors' => array_map(function (ConstraintViolation $violation) {
                return [
                    'path' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage()
                ];
            }, iterator_to_array($exception->getViolations()))
        ], Response::HTTP_BAD_REQUEST);

        $event->setResponse($response);
        $event->stopPropagation();
    }
}
