<?php

namespace App\EventListener;

use Gesdinet\JWTRefreshTokenBundle\Event\RefreshTokenNotFoundEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokenFailureListener
{
    public function onRefreshTokenFailure(RefreshTokenNotFoundEvent $event)
    {
        $response = new JsonResponse([
            'message' => 'Refresh token is invalid or expired',
            'error' => 'refresh_token_expired'
        ], Response::HTTP_UNAUTHORIZED);

        $event->setResponse($response);
    }
}
