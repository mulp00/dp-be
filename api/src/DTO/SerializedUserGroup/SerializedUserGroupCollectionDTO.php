<?php

namespace App\DTO\SerializedUserGroup;

use App\Entity\SerializedUserGroup;

class SerializedUserGroupCollectionDTO
{
    /**
     * @var SerializedUserGroupDTO[]
     */
    public array $serializedUserGroups;
    /**
     * @param SerializedUserGroup[] $serializedUserGroups
     */
    public function __construct(array $serializedUserGroups)
    {
        $this->serializedUserGroups = array_map(function (SerializedUserGroup $serializedUserGroup) {
            return $this->mapSerializedUserGroups($serializedUserGroup);
        }, $serializedUserGroups) ;
    }


    private function mapSerializedUserGroups(SerializedUserGroup $serializedUserGroup): SerializedUserGroupDTO{
        return new SerializedUserGroupDTO($serializedUserGroup);
    }

}
