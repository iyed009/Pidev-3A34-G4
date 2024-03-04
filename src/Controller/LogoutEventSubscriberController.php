<?php

namespace App\Controller;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutEventSubscriberController implements EventSubscriberInterface
{
    #[Route(path: '/logout', name: 'app_logout')]
    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }

    public function onLogout(LogoutEvent $event): void
    {
        $response = $event->getResponse();
        $response->headers->add([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => 'Wed, 11 Jan 1984 05:00:00 GMT',
        ]);
    }
}