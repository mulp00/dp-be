<?php

namespace App\DTO\User;

use App\Entity\User;

class PublicUserDTO
{
    public string $id;
    public string $email;
    public string $keyPackage;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->id = $user->getId();
        $this->email = $user->getEmail();
        $this->keyPackage = $user->getKeyPackage();
    }

    public function getId(): string
    {
        return $this->id;
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
