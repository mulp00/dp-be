<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

class JWTInvalidListener
{
    public function onJWTInvalid(JWTInvalidEvent $event)
    {
        $responseData = [
            'message' => 'Your token is invalid, please renew it.',
            'error' => 'token_invalid'
        ];

        $response = new JsonResponse($responseData, 401); // Use JsonResponse to set custom data

        $event->setResponse($response);
    }
}
