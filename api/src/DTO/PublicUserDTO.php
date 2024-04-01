<?php

namespace App\DTO;

use App\Entity\User;

class PublicUserDTO
{
    public string $email;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->email = $user->getEmail();
    }

    public function getEmail(): string
    {
        return $this->email;
    }


}
