<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

#[AsEventListener(event: ControllerEvent::class, method: 'onRequest')]
#[AsEventListener(event: ResponseEvent::class, method: 'onResponse')]
class RequestResponseListener
{
    public function onRequest(ControllerEvent $event): void
    {
        if (!$event->getRequest()->headers->contains('Accept', 'application/json')) {
            throw new NotAcceptableHttpException('Server only handles JSON requests with header Accept: application/json');
        }
    }

    public function onResponse(ResponseEvent $event): void
    {
    }
}
