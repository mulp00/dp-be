<?php

namespace App\DTO;

use App\Entity\WelcomeMessage;

class WelcomeMessageDTO
{
    public string $welcomeMessageId;
    public string $message;
    public string $ratchetTree;
    public int $epoch;
    public string $groupId;


    /**
     * @param WelcomeMessage $welcomeMessage
     */
    public function __construct(WelcomeMessage $welcomeMessage)
    {
        $this->welcomeMessageId = $welcomeMessage->getId();
        $this->message = $welcomeMessage->getMessage();
        $this->ratchetTree = $welcomeMessage->getRatchetTree();
        $this->epoch = $welcomeMessage->getCorrespondingMessage()->getEpoch();
        $this->groupId = $welcomeMessage->getTargetGroup()->getId();
    }

    public function getWelcomeMessageId(): string
    {
        return $this->welcomeMessageId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getRatchetTree(): string
    {
        return $this->ratchetTree;
    }

    public function getEpoch(): int
    {
        return $this->epoch;
    }

    public function getGroupId(): string
    {
        return $this->groupId;
    }


}
