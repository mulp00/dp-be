<?php

namespace App\DTO;

use App\Entity\Message;

class MessageDTO
{

    public string $message;
    public int $epoch;

    /**
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message->getMessage();
        $this->epoch = $message->getEpoch();
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getEpoch(): int
    {
        return $this->epoch;
    }


}
