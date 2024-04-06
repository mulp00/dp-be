<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationSuccessListener
{
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $response = $event->getResponse();
        $data = $event->getData();
        $refresh_token = $data['token'];

        dump($response);

        $cookie = new Cookie(
            'BEARER',
            $refresh_token,
            time() + 3600,
            '/',
            'localhost',
            true,
            true,
            false,
            'None' // TODO
        );
        $response->headers->setCookie($cookie);


        $event->setData($data);
    }
}
