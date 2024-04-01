<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\MFKDFPolicy\GetMFKDFByEmailController;
use App\Controller\SerializedUserGroup\CreateSerializedGroupController;
use App\Controller\SerializedUserGroup\GetSerializedUserGroupCollection;
use App\Controller\User\GetUserByEmailController;
use App\Controller\User\GetUserIdentityController;
use App\Repository\UserRepository;
use App\State\UserMasterKeyDoubleHasher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\OpenApi\Model;
use ArrayObject;


#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['tst']]),
        new Post(
            uriTemplate: '/auth/register',
            validationContext: ['groups' => ['Default', 'user:create', 'mfkdfpolicy:create']],
            processor: UserMasterKeyDoubleHasher::class),
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
        new Post( // TODO presunout do SerializedUserGroup, nejak tam nefunguje vyuziti controlleru i pres read:false
            uriTemplate: '/serializedGroup',
            controller: CreateSerializedGroupController::class,
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
                                    ]
                                ]
                            ]
                        ]
                    )
                )
            ),
            normalizationContext: ['groups' => ['serializedUserGroup:create']],
            denormalizationContext: ['groups' => ['serializedUserGroup:create']],
            validationContext: ['groups' => ['serializedUserGroup:create']],
            read: false,
        ),
        new Get(
            uriTemplate: '/serializedGroupCollection',
            controller: GetSerializedUserGroupCollection::class,
            read: false,
        ),
        new Get(
            uriTemplate: '/getByEmail/{id}',
            controller: GetUserByEmailController::class,
            read: false,
        ),
    ],
    normalizationContext: ['groups' => ['user:read', 'mfkdfpolicy:read']],
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
    #[Serializer\Groups(['user:read'])]
    private Uuid $id;

    #[ORM\Column(length: 180)]
    #[Groups(['user:read', 'user:create', 'user:update', 'userCollection:read', 'serializedUserGroup:create', 'user:identity'])]
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

    #[Assert\NotBlank(groups: ['user:create'])]
    #[Groups(['user:create', 'user:update'])]
    private ?string $masterKey = null;

    #[ORM\OneToOne(mappedBy: 'policyUser', cascade: ['persist', 'remove'])]
    #[Groups(['user:create', 'user:update', 'mfkdfpolicy:read'])]
    private ?MFKDFPolicy $mfkdfpolicy = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['user:create', 'user:update', 'user:read', 'user:identity'])]
    private ?string $serializedIdentity = null;

    #[ORM\OneToMany(mappedBy: 'groupUser', targetEntity: SerializedUserGroup::class, orphanRemoval: true)]
    #[Groups(['serializedUserGroup:read', 'serializedUserGroup:create'])]
    private Collection $serializedUserGroups;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'users')]
    private Collection $groups;

    #[ORM\Column(type: 'text')]
    #[Groups(['user:create'])]
    private ?string $keyPackage = null;

    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Group::class)]
    private Collection $createdGroups;

    public function __construct()
    {
        $this->serializedUserGroups = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->createdGroups = new ArrayCollection();
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

    public function getMasterKey(): ?string
    {
        return $this->masterKey;
    }

    public function setMasterKey(?string $masterKey): void
    {
        $this->masterKey = $masterKey;
    }


    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->masterKey = null;
    }

    public function getMfkdfpolicy(): ?MFKDFPolicy
    {
        return $this->mfkdfpolicy;
    }

    public function setMfkdfpolicy(MFKDFPolicy $mfkdfpolicy): static
    {
        // set the owning side of the relation if necessary
        if ($mfkdfpolicy->getPolicyUser() !== $this) {
            $mfkdfpolicy->setPolicyUser($this);
        }

        $this->mfkdfpolicy = $mfkdfpolicy;

        return $this;
    }

    public function getSerializedIdentity(): ?string
    {
        return $this->serializedIdentity;
    }

    public function setSerializedIdentity(?string $serializedIdentity): void
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

}
