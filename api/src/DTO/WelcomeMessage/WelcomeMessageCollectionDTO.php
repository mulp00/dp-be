<?php

namespace App\DTO\WelcomeMessage;

use App\Entity\WelcomeMessage;

class WelcomeMessageCollectionDTO
{
    /**
     * @var WelcomeMessageDTO[]
     */
    public array $welcomeMessages;

    /**
     * @param WelcomeMessage[] $welcomeMessages
     */
    public function __construct(array $welcomeMessages)
    {
        $this->welcomeMessages = array_map(function (WelcomeMessage $welcomeMessage) {
            return $this->mapMessages($welcomeMessage);
        }, $welcomeMessages) ;
    }
    private function mapMessages(WelcomeMessage $message): WelcomeMessageDTO{
        return new WelcomeMessageDTO($message);
    }

}
