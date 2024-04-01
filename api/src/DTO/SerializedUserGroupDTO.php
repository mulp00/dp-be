<?php

namespace App\DTO;

use App\Entity\SerializedUserGroup;
use App\Entity\User;

class SerializedUserGroupDTO
{
    public string $name;
    public string $serializedGroup;

    /**
     * @var PublicUserDTO[]
     */
    public array $users;

    /**
     * @param SerializedUserGroup $serializedUserGroup
     */
    public function __construct(SerializedUserGroup $serializedUserGroup)
    {
        $this->name = $serializedUserGroup->getGroupEntity()->getName();
        $this->serializedGroup = $serializedUserGroup->getSerializedGroup();
        $this->users = array_map(function (User $user) {
            // This calls the mapUsers method for each user
            return $this->mapUsers($user);
        }, $serializedUserGroup->getGroupEntity()->getUsers()->toArray()) ;
    }

    private function mapUsers(User $user): PublicUserDTO{
        return new PublicUserDTO($user);
    }

    public function getSerializedGroup(): string
    {
        return $this->serializedGroup;
    }

    public function getUsers(): array
    {
        return $this->users;
    }

    public function getName(): string
    {
        return $this->name;
    }




}
