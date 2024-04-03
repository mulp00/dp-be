<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ApiResource(operations: [
    new Get()
])]
#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Serializer\Groups(['group:read', 'group:create', 'serializedUserGroup:create', 'serializedUserGroup:read'])]
    private Uuid $id;

    #[ORM\Column(length: 180)]
    #[Groups(['group:create', 'group:read', 'serializedUserGroup:create'])]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'groups', fetch: 'EAGER')]
    #[Serializer\Groups(['group:read', 'group:create', 'serializedUserGroup:create', 'serializedUserGroup:read'])]
    private Collection $users;

    #[ORM\ManyToOne(inversedBy: 'createdGroups')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['group:create', 'group:read', 'serializedUserGroup:create', 'serializedUserGroup:read'])]
    private ?User $creator = null;

    #[ORM\OneToMany(mappedBy: 'targetGroup', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    #[ORM\Column]
    private ?int $epoch = null;

    /**
     * @param string|null $name
     * @param User $creator
     */
    public function __construct(?string $name, User $creator)
    {
        $this->name = $name;
        $this->users = new ArrayCollection([$creator]);
        $this->creator = $creator;
        $this->messages = new ArrayCollection();
        $this->epoch = 1;
    }


    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): static
    {
        $this->creator = $creator;

        return $this;
    }


    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setTargetGroup($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getTargetGroup() === $this) {
                $message->setTargetGroup(null);
            }
        }

        return $this;
    }

    public function getEpoch(): ?int
    {
        return $this->epoch;
    }

    public function setEpoch(?int $epoch): void
    {
        $this->epoch = $epoch;
    }

    public function addEpoch(): void
    {
        $this->epoch = $this->epoch +1;
    }




}
