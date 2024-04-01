<?php

namespace App\DTO;

use App\Entity\SerializedUserGroup;
use App\Entity\User;

class SerializedUserGroupDTO
{
    public string $name;
    public string $serializedGroup;
    public PublicUserDTO $creator;

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
        $this->users = (new PublicUserGroupDTO($serializedUserGroup->getGroupEntity()->getUsers()->toArray()))->getUsers();
        $this->creator = new PublicUserDTO($serializedUserGroup->getGroupEntity()->getCreator());
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

    public function getCreator(): PublicUserDTO
    {
        return $this->creator;
    }




}