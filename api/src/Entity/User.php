<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Controller\Group\GetGroupsToJoin;
use App\Controller\SerializedUserGroup\GetSerializedUserGroupCollection;
use App\Controller\User\GetMFKDFByEmailController;
use App\Controller\User\GetUserByEmailController;
use App\Controller\User\GetUserIdentityController;
use App\Controller\User\PatchKeyStoreController;
use App\Controller\User\RegisterController;
use App\Controller\User\UpdateKeyPackageController;
use App\Repository\UserRepository;
use ArrayObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;


#[ApiResource(
    operations: [
        new Get(),
        new Post(
            uriTemplate: '/auth/register',
            controller: RegisterController::class,
            read: false,
        ),
        new Get(uriTemplate: '/auth/user/{id}/policy',
            controller: GetMFKDFByEmailController::class,
            openapiContext: [
                'summary' => 'Retrieve MFKDF policy for a user by email',
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                            'format' => 'email'
                        ],
                        'description' => 'The email of the user to retrieve the policy for.',
                    ]
                ]
            ],
            normalizationContext: ['groups' => ['mfkdfpolicy:read']],
            validationContext: ['groups' => ['mfkdfpolicy:read']],
            read: false

        ),
        new Get(
            uriTemplate: '/me',
            controller: GetUserIdentityController::class,
            normalizationContext: ['groups' => ['user:identity']],
            read: false
        ),

        new Get(
            uriTemplate: '/me/serialized-user-groups',
            controller: GetSerializedUserGroupCollection::class,
            read: false,
        ),
        new Get(
            uriTemplate: '/users',
            controller: GetUserByEmailController::class,
            output: false,
            read: false,
        ),
        new Get(
            uriTemplate: '/me/groups-to-join',
            controller: GetGroupsToJoin::class,
            output: false,
            read: false,
        ),
//        new Patch(
//            uriTemplate: '/groups/{id}/ratchet-tree',
//            controller: UpdateRatchetTreeController::class,
//            openapi: new Model\Operation(
//                requestBody: new Model\RequestBody(
//                    content: new ArrayObject([
//                            'application/merge-patch+json' => [
//                                'schema' => [
//                                    'type' => 'object',
//                                    'properties' => [
//                                        'ratchetTree' => [
//                                            'type' => 'string',
//                                        ],
//                                    ]
//                                ]
//                            ]
//                        ]
//                    )
//                )
//            ),
//            read: false,
//        ),

        new Patch(
            uriTemplate: '/me/key-store',
            controller: PatchKeyStoreController::class,
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new ArrayObject([
                            'application/merge-patch+json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'keyStore' => [
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
        ),
        new Patch(
            uriTemplate: '/me/key-package',
            controller: UpdateKeyPackageController::class,
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new ArrayObject([
                            'application/merge-patch+json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'keyPackage' => [
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
        ),

    ],
    normalizationContext: ['groups' => ['mfkdfpolicy:read']],
    denormalizationContext: ['groups' => ['user:create', 'user:update', 'mfkdfpolicy:read']],
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(length: 180)]
    #[Groups(['user:create', 'user:update', 'userCollection:read', 'serializedUserGroup:create', 'user:identity'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string|null The hashed master key
     */
    #[ORM\Column]
    private ?string $masterKeyHash = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['mfkdfpolicy:read', 'mfkdfpolicy:create', 'mfkdfpolicy:update', 'user:create', 'user:update'])]
    private string $mfkdfpolicy;

    #[ORM\Column(type: 'array', nullable: true)]
    #[Groups(['user:create', 'user:update', 'user:identity'])]
    private ?array $serializedIdentity = null;

    #[ORM\OneToMany(mappedBy: 'groupUser', targetEntity: SerializedUserGroup::class, orphanRemoval: true)]
    #[Groups(['serializedUserGroup:read', 'serializedUserGroup:create'])]
    private Collection $serializedUserGroups;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'users')]
    private Collection $groups;

    #[ORM\Column(type: 'text')]
    #[Groups(['user:create', 'user:identity'])]
    private ?string $keyPackage = null;

    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Group::class)]
    private Collection $createdGroups;

    #[ORM\OneToMany(mappedBy: 'recipient', targetEntity: WelcomeMessage::class, fetch: 'EAGER', orphanRemoval: true)]
    private Collection $welcomeMessages;

    #[ORM\Column(type: 'array', nullable: true)]
    #[Groups(['user:create', 'user:update', 'user:identity'])]
    private ?array $keyStore = null;

    public function __construct()
    {
        $this->serializedUserGroups = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->createdGroups = new ArrayCollection();
        $this->welcomeMessages = new ArrayCollection();
    }


    public function getSerializedUserGroupByGroup(Group $group): ?SerializedUserGroup
    {

        /** @var SerializedUserGroup $serializedUserGroup */
        foreach ($this->serializedUserGroups as $serializedUserGroup) {
            if ($serializedUserGroup->getGroupEntity() === $group) {
                return $serializedUserGroup;
            }
        }
        return null;
    }


    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @return list<string>
     * @see UserInterface
     *
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getMasterKeyHash(): string
    {
        return $this->masterKeyHash;
    }

    /**
     * needed for the internal JWT generation
     * @return string
     */
    public function getPassword(): string
    {
        return $this->masterKeyHash;
    }

    public function setMasterKeyHash(string $masterKeyHash): static
    {
        $this->masterKeyHash = $masterKeyHash;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here

    }

    public function getMfkdfpolicy(): string
    {
        return $this->mfkdfpolicy;
    }

    public function setMfkdfpolicy(string $mfkdfpolicy): void
    {
        $this->mfkdfpolicy = $mfkdfpolicy;
    }

    public function getSerializedIdentity(): ?array
    {
        return $this->serializedIdentity;
    }

    public function setSerializedIdentity(?array $serializedIdentity): void
    {
        $this->serializedIdentity = $serializedIdentity;
    }

    /**
     * @return Collection<int, SerializedUserGroup>
     */
    public function getSerializedUserGroups(): Collection
    {
        return $this->serializedUserGroups;
    }

    public function addSerializedUserGroup(SerializedUserGroup $serializedUserGroup): static
    {
        if (!$this->serializedUserGroups->contains($serializedUserGroup)) {
            $this->serializedUserGroups->add($serializedUserGroup);
            $serializedUserGroup->setGroupUser($this);
        }

        return $this;
    }

    public function removeSerializedUserGroup(SerializedUserGroup $serializedUserGroup): static
    {
        if ($this->serializedUserGroups->removeElement($serializedUserGroup)) {
            // set the owning side to null (unless already changed)
            if ($serializedUserGroup->getGroupUser() === $this) {
                $serializedUserGroup->setGroupUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): static
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
            $group->addUser($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): static
    {
        if ($this->groups->removeElement($group)) {
            $group->removeUser($this);
        }

        return $this;
    }

    public function getKeyPackage(): ?string
    {
        return $this->keyPackage;
    }

    public function setKeyPackage(string $keyPackage): static
    {
        $this->keyPackage = $keyPackage;

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getCreatedGroups(): Collection
    {
        return $this->createdGroups;
    }

    public function addCreatedGroup(Group $createdGroup): static
    {
        if (!$this->createdGroups->contains($createdGroup)) {
            $this->createdGroups->add($createdGroup);
            $createdGroup->setCreator($this);
        }

        return $this;
    }

    public function removeCreatedGroup(Group $createdGroup): static
    {
        if ($this->createdGroups->removeElement($createdGroup)) {
            // set the owning side to null (unless already changed)
            if ($createdGroup->getCreator() === $this) {
                $createdGroup->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WelcomeMessage>
     */
    public function getWelcomeMessages(): Collection
    {
        return $this->welcomeMessages;
    }

    public function addWelcomeMessage(WelcomeMessage $welcomeMessage): static
    {
        if (!$this->welcomeMessages->contains($welcomeMessage)) {
            $this->welcomeMessages->add($welcomeMessage);
            $welcomeMessage->setRecipient($this);
        }

        return $this;
    }

    public function removeWelcomeMessage(WelcomeMessage $welcomeMessage): static
    {
        if ($this->welcomeMessages->removeElement($welcomeMessage)) {
            // set the owning side to null (unless already changed)
            if ($welcomeMessage->getRecipient() === $this) {
                $welcomeMessage->setRecipient(null);
            }
        }

        return $this;
    }

    public function getKeyStore(): ?array
    {
        return $this->keyStore;
    }

    public function setKeyStore(?array $keyStore): void
    {
        $this->keyStore = $keyStore;
    }


}
