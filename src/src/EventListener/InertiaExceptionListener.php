<?php

namespace App\EventListener;

use Nytodev\InertiaBundle\Service\Inertia;
use Symfony\Component\HttpFoundation\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class InertiaExceptionListener
{
    public function __construct(private Inertia $inertia,)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof NotFoundHttpException) {
            $response = $this->inertia->render('Errors/404', []);
            $event->setResponse($response);
        }
    }
}