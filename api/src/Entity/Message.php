<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $targetGroup = null;

    #[ORM\Column]
    private ?int $epoch = null;

    #[ORM\Column(type: 'text')]
    private ?string $message = null;

    /**
     * @param Group|null $targetGroup
     * @param int|null $epoch
     * @param string|null $message
     */
    public function __construct(?Group $targetGroup, ?int $epoch, ?string $message)
    {
        $this->targetGroup = $targetGroup;
        $this->epoch = $epoch;
        $this->message = $message;
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

    public function getEpoch(): ?int
    {
        return $this->epoch;
    }

    public function setEpoch(int $epoch): static
    {
        $this->epoch = $epoch;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

}
