<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

class JWTExpiredListener
{
    public function onJWTExpired(JWTExpiredEvent $event)
    {
        $responseData = [
            'message' => 'Your token is expired, please renew it.',
            'error' => 'token_expired'
        ];

        $response = new JsonResponse($responseData, 401); // Use JsonResponse to set custom data

        $event->setResponse($response);
    }
}
