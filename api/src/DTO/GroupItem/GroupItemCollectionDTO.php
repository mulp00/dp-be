<?php

namespace App\DTO\GroupItem;

use App\Entity\GroupItem;

class GroupItemCollectionDTO
{

    /**
     * @var GroupItemDTO[]
     */
    public array $groupItems;

    /**
     * @param GroupItem[] $groupItems
     */
    public function __construct(array $groupItems)
    {
        $this->groupItems = array_map(function (GroupItem $groupItem) {
            return $this->mapGroupItems($groupItem);
        }, $groupItems);
    }


    private function mapGroupItems(GroupItem $groupItem): GroupItemDTO
    {
        return new GroupItemDTO($groupItem);
    }
}
