<?php

namespace App\DTO\Message;

use App\Entity\Message;

class MessageCollectionDTO
{

    /**
     * @var MessageDTO[]
     */
    public array $messages;
    /**
     * @param Message[] $messages
     */
    public function __construct(array $messages)
    {
        $this->messages = array_map(function (Message $message) {
            return $this->mapMessages($message);
        }, $messages) ;
    }


    private function mapMessages(Message $message): MessageDTO{
        return new MessageDTO($message);
    }

}
