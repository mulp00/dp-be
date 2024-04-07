<?php

namespace App\DTO\GroupItem;

use App\Entity\GroupItem;

class GroupItemDTO
{
    public string $id;
    public string $name;
    public string $description;
    public string $groupId;
    public string $type;
    public array $content;

    /**
     * @param GroupItem $groupItem
     */
    public function __construct(GroupItem $groupItem)
    {
        $this->id = $groupItem->getId();
        $this->name = $groupItem->getName();
        $this->description = $groupItem->getDescription();
        $this->groupId = $groupItem->getTargetGroup()->getId();
        $this->type = $groupItem->getType()->value;
        $this->content = $groupItem->getContent();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }



    public function getGroupId(): string
    {
        return $this->groupId;
    }


    public function getType(): string
    {
        return $this->type;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function getId(): string
    {
        return $this->id;
    }



}
