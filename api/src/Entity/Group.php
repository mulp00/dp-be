<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Group\CreateNewGroupController;
use App\Controller\Group\DeleteGroupController;
use App\Controller\Group\LeaveGroupController;
use App\Controller\Group\RemoveUserFromGroupController;
use App\Controller\GroupItem\CreateGroupItemController;
use App\Controller\GroupItem\DeleteGroupItemController;
use App\Controller\GroupItem\GetGroupItemCollectionController;
use App\Controller\GroupItem\UpdateGroupItemController;
use App\Controller\Message\CreateGeneralCommitMessageController;
use App\Controller\Message\GetMessagesCollectionController;
use App\Controller\WelcomeMessage\CreateWelcomeMessage;
use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use ApiPlatform\OpenApi\Model;
use ArrayObject;

#[ApiResource(operations: [
    new Get(),
    new Post(
        uriTemplate: '/groups',
        controller: CreateNewGroupController::class,
        openapi: new Model\Operation(
            requestBody: new Model\RequestBody(
                content: new ArrayObject([
                        'application/ld+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'serializedGroup' => [
                                        'type' => 'string',
                                    ],
                                    'name' => [
                                        'type' => 'string',
                                    ],
                                    'ratchetTree' => [
                                        'type' => 'string',
                                    ],
                                ]
                            ]
                        ]
                    ]
                )
            )
        ),
        read: false,
        deserialize: false,
    ),
    new Get(
        uriTemplate: '/groups/{id}/messages',
        controller: GetMessagesCollectionController::class,
        openapi: new Model\Operation(
            requestBody: new Model\RequestBody(
                content: new ArrayObject([
                        'application/ld+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'epoch' => [
                                        'type' => 'string',
                                    ],
                                ]
                            ]
                        ]
                    ]
                )
            )
        ),
        read: false,
        deserialize: false,
    ),
    new Post(
        uriTemplate: '/groups/{id}/welcome-messages',
        controller: CreateWelcomeMessage::class,
        openapi: new Model\Operation(
            requestBody: new Model\RequestBody(
                content: new ArrayObject([
                        'application/ld+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'memberId' => [
                                        'type' => 'string',
                                    ],
                                    'welcomeMessage' => [
                                        'type' => 'string',
                                    ],
                                    'commitMessage' => [
                                        'type' => 'string',
                                    ],
                                    'ratchetTree' => [
                                        'type' => 'string',
                                    ],
                                ]
                            ]
                        ]
                    ]
                )
            )
        ),
        read: false,
        deserialize: false,
    ),
    new Delete(
        uriTemplate: '/groups/{id}/users/{userId}',
        controller: RemoveUserFromGroupController::class,
        openapi: new Model\Operation(
            requestBody: new Model\RequestBody(
                content: new ArrayObject([
                        'application/ld+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'message' => [
                                        'type' => 'string',
                                    ],
                                    'epoch' => [
                                        'type' => 'string',
                                    ],
                                ]
                            ]
                        ]
                    ]
                )
            )
        ),
        read: false,
        deserialize: false,
    ),
    new Post(
        uriTemplate: '/groups/{id}/messages',
        controller: CreateGeneralCommitMessageController::class,
        openapi: new Model\Operation(
            requestBody: new Model\RequestBody(
                content: new ArrayObject([
                        'application/ld+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'message' => [
                                        'type' => 'string',
                                    ],
                                    'epoch' => [
                                        'type' => 'string',
                                    ],
                                ]
                            ]
                        ]
                    ]
                )
            )
        ),
        read: false,
        deserialize: false,
    ),
    new Post(
        uriTemplate: '/groups/{id}/group-items',
        controller: CreateGroupItemController::class,
        openapi: new Model\Operation(
            requestBody: new Model\RequestBody(
                content: new ArrayObject([
                        'application/ld+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'name' => [
                                        'type' => 'string',
                                    ],
                                    'description' => [
                                        'type' => 'string',
                                    ],
                                    'type' => [
                                        'type' => 'string',
                                    ],
                                    'content' => [
                                        'type' => 'string',
                                    ],
                                ]
                            ]
                        ]
                    ]
                )
            )
        ),
        read: false,
        deserialize: false,
    ),
    new Patch(
        uriTemplate: '/groups/{id}/group-items/{groupItem}',
        controller: UpdateGroupItemController::class,
        openapi: new Model\Operation(
            requestBody: new Model\RequestBody(
                content: new ArrayObject([
                        'application/merge-patch+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'itemId' => [
                                        'type' => 'string',
                                    ],
                                    'name' => [
                                        'type' => 'string',
                                    ],
                                    'description' => [
                                        'type' => 'string',
                                    ],
                                    'type' => [
                                        'type' => 'string',
                                    ],
                                    'content' => [
                                        'type' => 'string',
                                    ],
                                    'epoch' => [
                                        'type' => 'string',
                                    ],
                                ]
                            ]
                        ]
                    ]
                )
            )
        ),
        read: false,
        deserialize: false,
    ),
    new Delete(
        uriTemplate: '/groups/{id}/group-items/{groupItem}',
        controller: DeleteGroupItemController::class,
        read: false,
        deserialize: false,
    ),
    new Delete(
        uriTemplate: '/groups/{id}',
        controller: DeleteGroupController::class,
        read: false,
        deserialize: false,
    ),
    new Post(
        uriTemplate: '/groups/{id}/leave',
        controller: LeaveGroupController::class,
        openapi: new Model\Operation(
            requestBody: new Model\RequestBody(
                content: new ArrayObject([
                        'application/ld+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'message' => [
                                        'type' => 'string',
                                    ],
                                    'epoch' => [
                                        'type' => 'string',
                                    ],
                                ]
                            ]
                        ]
                    ]
                )
            )
        ),
        read: false,
        deserialize: false,
    ),
    new Get(
        uriTemplate: '/groups/{id}/group-items',
        controller: GetGroupItemCollectionController::class,
        read: false,
        deserialize: false,
    )
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

    #[ORM\OneToMany(mappedBy: 'targetGroup', targetEntity: GroupItem::class, fetch: 'EAGER', orphanRemoval: true)]
    private Collection $groupItems;

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
        $this->groupItems = new ArrayCollection();
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
        $this->epoch = $this->epoch + 1;
    }

    /**
     * @return Collection<int, GroupItem>
     */
    public function getGroupItems(): Collection
    {
        return $this->groupItems;
    }

    public function addGroupItem(GroupItem $groupItem): static
    {
        if (!$this->groupItems->contains($groupItem)) {
            $this->groupItems->add($groupItem);
            $groupItem->setTargetGroup($this);
        }

        return $this;
    }

    public function removeGroupItem(GroupItem $groupItem): static
    {
        if ($this->groupItems->removeElement($groupItem)) {
            // set the owning side to null (unless already changed)
            if ($groupItem->getTargetGroup() === $this) {
                $groupItem->setTargetGroup(null);
            }
        }

        return $this;
    }


}
