<?php

namespace App\Entity;

use App\Repository\SerializedUserGroupRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Entity(repositoryClass: SerializedUserGroupRepository::class)]
class SerializedUserGroup
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'serializedUserGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $groupUser = null;

    #[ORM\Column(type: 'text', nullable: false)]
    #[Serializer\Groups(['serializedUserGroup:read'])]
    private ?string $serializedGroup = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Serializer\Groups(['serializedUserGroup:read'])]
    private ?Group $groupEntity = null;

    /**
     * @param User|null $groupUser
     * @param string|null $serializedGroup
     * @param Group|null $groupEntity
     */
    public function __construct(?User $groupUser, ?string $serializedGroup, ?Group $groupEntity)
    {
        $this->groupUser = $groupUser;
        $this->serializedGroup = $serializedGroup;
        $this->groupEntity = $groupEntity;
    }


    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getGroupUser(): ?User
    {
        return $this->groupUser;
    }

    public function setGroupUser(?User $groupUser): static
    {
        $this->groupUser = $groupUser;

        return $this;
    }

    public function getSerializedGroup(): ?string
    {
        return $this->serializedGroup;
    }

    public function setSerializedGroup(string $serializedGroup): static
    {
        $this->serializedGroup = $serializedGroup;

        return $this;
    }

    public function getGroupEntity(): ?Group
    {
        return $this->groupEntity;
    }

    public function setGroupEntity(?Group $groupEntity): static
    {
        $this->groupEntity = $groupEntity;

        return $this;
    }
}
