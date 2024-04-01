<?php

namespace App\DTO;

use App\Entity\User;

class PublicUserDTO
{
    public string $email;
    public string $keyPackage;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->email = $user->getEmail();
        $this->keyPackage = $user->getKeyPackage();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getKeyPackage(): string
    {
        return $this->keyPackage;
    }



}
