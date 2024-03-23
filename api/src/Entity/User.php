<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Controller\MFKDFPolicyControllers\GetMFKDFByEmailController;
use App\Repository\UserRepository;
use App\State\UserMasterKeyDoubleHasher;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Uid\Uuid;
use ApiPlatform\OpenApi\Model;


#[ApiResource(
    operations: [
        new Post(uriTemplate: '/auth/register', validationContext: ['groups' => ['Default', 'user:create', 'mfkdfpolicy:create']],
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

        )
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
    private Uuid|null $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['user:read', 'user:create', 'user:update'])]
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

}
