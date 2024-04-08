<?php

namespace App\DTO\SerializedUserGroup;

use App\DTO\User\PublicUserCollectionDTO;
use App\DTO\User\PublicUserDTO;
use App\Entity\SerializedUserGroup;
use Symfony\Component\Uid\Uuid;

class SerializedUserGroupDTO
{
    public Uuid $groupId;
    public Uuid $serializedUserGroupId;
    public string $name;
    public array $serializedGroup;
    public PublicUserDTO $creator;
    public int $lastEpoch;
    public int $epoch;


    /**
     * @var PublicUserDTO[]
     */
    public array $users;

    /**
     * @param SerializedUserGroup $serializedUserGroup
     */
    public function __construct(SerializedUserGroup $serializedUserGroup)
    {
        $this->serializedUserGroupId = $serializedUserGroup->getId();
        $this->groupId = $serializedUserGroup->getGroupEntity()->getId();
        $this->name = $serializedUserGroup->getGroupEntity()->getName();
        $this->serializedGroup = $serializedUserGroup->getSerializedGroup();
        $this->users = (new PublicUserCollectionDTO($serializedUserGroup->getGroupEntity()->getUsers()->toArray()))->getUsers();
        $this->creator = new PublicUserDTO($serializedUserGroup->getGroupEntity()->getCreator());
        $this->lastEpoch = $serializedUserGroup->getLastEpoch();
        $this->epoch = $serializedUserGroup->getGroupEntity()->getEpoch();
    }

    public function getGroupId(): Uuid
    {
        return $this->groupId;
    }

    public function getSerializedUserGroupId(): Uuid
    {
        return $this->serializedUserGroupId;
    }

    public function getSerializedGroup(): array
    {
        return $this->serializedGroup;
    }

    public function getEpoch(): int
    {
        return $this->epoch;
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

    public function getLastEpoch(): int
    {
        return $this->lastEpoch;
    }




}
