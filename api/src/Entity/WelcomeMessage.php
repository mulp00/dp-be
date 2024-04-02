<?php

namespace App\Entity;

use App\Repository\WelcomeMessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;


#[ORM\Entity(repositoryClass: WelcomeMessageRepository::class)]
#[ORM\UniqueConstraint(name: "user_group_unique", columns: ["recipient_id", "target_group_id"])]
class WelcomeMessage
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'welcomeMessages')]
    #[ORM\JoinColumn(name: 'recipient_id', nullable: false)]
    private ?User $recipient = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'target_group_id', nullable: false)]
    private ?Group $targetGroup = null;

    #[ORM\Column(type: 'text')]
    private ?string $message = null;

    /**
     * @param User|null $recipient
     * @param Group|null $targetGroup
     * @param string|null $message
     */
    public function __construct(?User $recipient, ?Group $targetGroup, ?string $message)
    {
        $this->recipient = $recipient;
        $this->targetGroup = $targetGroup;
        $this->message = $message;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }


    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function setRecipient(?User $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
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
