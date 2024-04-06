<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\GroupItemType;
use App\Repository\GroupItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: GroupItemRepository::class)]
#[ApiResource]
class GroupItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;
    #[ORM\Column(length: 255)]
    private ?string $name = null;
    #[ORM\ManyToOne(inversedBy: 'groupItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $targetGroup = null;

   #[ORM\Column(type:"string", length:255, enumType:GroupItemType::class)]
    private ?GroupItemType $type = null;

   #[ORM\Column(type: 'text',length: 255)]
   private ?string $content = null;

   #[ORM\Column(length: 255)]
   private ?string $iv = null;

   #[ORM\Column(length: 255)]
   private ?string $description = null;

    /**
     * @param string|null $name
     * @param Group|null $targetGroup
     * @param GroupItemType|null $type
     * @param string|null $content
     * @param string|null $iv
     * @param string|null $description
     */
    public function __construct(?string $name, ?Group $targetGroup, ?GroupItemType $type, ?string $content, ?string $iv, ?string $description)
    {
        $this->name = $name;
        $this->targetGroup = $targetGroup;
        $this->type = $type;
        $this->content = $content;
        $this->iv = $iv;
        $this->description = $description;
    }


    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTargetGroup(): ?Group
    {
        return $this->targetGroup;
    }

    public function setTargetGroup(?Group $targetGroup): static
    {
        $this->targetGroup = $targetGroup;

        return $this;
    }

    public function getType(): ?GroupItemType
    {
        return $this->type;
    }

    public function setType(?GroupItemType $type): void
    {
        $this->type = $type;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getIv(): ?string
    {
        return $this->iv;
    }

    public function setIv(string $iv): static
    {
        $this->iv = $iv;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
