<?php

namespace AppBundle\EventListener;

use Doctrine\DBAL\Exception\ConnectionException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class PDOExceptionListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onPDOException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof ConnectionException) {
            //TODO create error page
        }
    }
}
