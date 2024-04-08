<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\SerializedUserGroupRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new Get()
    ]
)]
#[ORM\Entity(repositoryClass: SerializedUserGroupRepository::class)]
class SerializedUserGroup
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'serializedUserGroups')]
    #[Serializer\Groups(['serializedUserGroup:create'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $groupUser = null;

    #[ORM\Column(type: 'array', nullable: false)]
    #[Serializer\Groups(['serializedUserGroup:read', 'serializedUserGroup:create'])]
    private ?array $serializedGroup = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Serializer\Groups(['serializedUserGroup:read', 'serializedUserGroup:create'])]
    private ?Group $groupEntity = null;

    #[ORM\Column]
    private ?int $lastEpoch = null;

    /**
     * @param User|null $groupUser
     * @param array|null $serializedGroup
     * @param Group|null $groupEntity
     * @param int $messageEpoch
     */
    public function __construct(?User $groupUser, ?array $serializedGroup, ?Group $groupEntity, int $messageEpoch)
    {
        $this->groupUser = $groupUser;
        $this->serializedGroup = $serializedGroup;
        $this->groupEntity = $groupEntity;
        $this->lastEpoch = $messageEpoch;
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

    public function getSerializedGroup(): ?array
    {
        return $this->serializedGroup;
    }

    public function setSerializedGroup(?array $serializedGroup): void
    {
        $this->serializedGroup = $serializedGroup;
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

    public function getLastEpoch(): ?int
    {
        return $this->lastEpoch;
    }

    public function setLastEpoch(int $lastEpoch): static
    {
        $this->lastEpoch = $lastEpoch;

        return $this;
    }
}
