<?php

namespace App\DTO;

use App\Entity\WelcomeMessage;

class WelcomeMessageDTO
{
    public string $message;
    public string $ratchetTree;

    /**
     * @param WelcomeMessage $welcomeMessage
     */
    public function __construct(WelcomeMessage $welcomeMessage)
    {
        $this->message = $welcomeMessage->getMessage();
        $this->ratchetTree = $welcomeMessage->getTargetGroup()->getRatchetTree();
    }


    public function getMessage(): string
    {
        return $this->message;
    }

    public function getRatchetTree(): string
    {
        return $this->ratchetTree;
    }



}
