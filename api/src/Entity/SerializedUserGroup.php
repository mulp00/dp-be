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

    #[ORM\Column(type: 'text', nullable: false)]
    #[Serializer\Groups(['serializedUserGroup:read', 'serializedUserGroup:create'])]
    private ?string $serializedGroup = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Serializer\Groups(['serializedUserGroup:read', 'serializedUserGroup:create'])]
    private ?Group $groupEntity = null;

    #[ORM\Column]
    private ?int $lastMessageSequenceNumber = null;

    /**
     * @param User|null $groupUser
     * @param string|null $serializedGroup
     * @param Group|null $groupEntity
     * @param int $messageSequenceNumber
     */
    public function __construct(?User $groupUser, ?string $serializedGroup, ?Group $groupEntity, int $messageSequenceNumber)
    {
        $this->groupUser = $groupUser;
        $this->serializedGroup = $serializedGroup;
        $this->groupEntity = $groupEntity;
        $this->lastMessageSequenceNumber = $messageSequenceNumber;
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

    public function getLastMessageSequenceNumber(): ?int
    {
        return $this->lastMessageSequenceNumber;
    }

    public function setLastMessageSequenceNumber(int $lastMessageSequenceNumber): static
    {
        $this->lastMessageSequenceNumber = $lastMessageSequenceNumber;

        return $this;
    }
}
