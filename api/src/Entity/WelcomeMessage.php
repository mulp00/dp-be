<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\WelcomeMessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ApiResource(operations:[new Get()])]
#[ORM\Entity(repositoryClass: WelcomeMessageRepository::class)]
#[ORM\UniqueConstraint(name: "user_group_unique", columns: ["recipient_id", "target_group_id", "message"])]
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

    #[ORM\Column(name: 'message', type: 'text')]
    private ?string $message = null;

    #[ORM\OneToOne(cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Message $correspondingMessage = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['group:create', 'group:read', 'serializedUserGroup:create', 'serializedUserGroup:read'])]
    private ?string $ratchetTree = null;

    #[ORM\Column]
    private ?bool $isJoined = null;

    /**
     * @param User|null $recipient
     * @param Group|null $targetGroup
     * @param string|null $message
     * @param Message|null $correspondingMessage
     * @param string|null $ratchetTree
     */
    public function __construct(?User $recipient, ?Group $targetGroup, ?string $message, ?Message $correspondingMessage, ?string $ratchetTree)
    {
        $this->recipient = $recipient;
        $this->targetGroup = $targetGroup;
        $this->message = $message;
        $this->correspondingMessage = $correspondingMessage;
        $this->ratchetTree = $ratchetTree;
        $this->isJoined = false;
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

    public function getCorrespondingMessage(): ?Message
    {
        return $this->correspondingMessage;
    }

    public function setCorrespondingMessage(Message $correspondingMessage): static
    {
        $this->correspondingMessage = $correspondingMessage;

        return $this;
    }

    public function isIsJoined(): ?bool
    {
        return $this->isJoined;
    }

    public function setIsJoined(bool $isJoined): static
    {
        $this->isJoined = $isJoined;

        return $this;
    }

    public function getRatchetTree(): ?string
    {
        return $this->ratchetTree;
    }

    public function setRatchetTree(?string $ratchetTree): void
    {
        $this->ratchetTree = $ratchetTree;
    }
}
