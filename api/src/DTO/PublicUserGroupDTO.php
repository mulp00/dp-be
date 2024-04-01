<?php

namespace App\DTO;

use App\Entity\User;

class PublicUserGroupDTO
{

    /**
     * @var PublicUserDTO[]
     */
    public array $users;

    /**
     * @param User[] $users
     */
    public function __construct(array $users)
    {
        $this->users = array_map(function (User $user) {
            return $this->mapUsers($user);
        }, $users) ;
    }


    private function mapUsers(User $user): PublicUserDTO{
        return new PublicUserDTO($user);
    }

    public function getUsers(): array
    {
        return $this->users;
    }




}
