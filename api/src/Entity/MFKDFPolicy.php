<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Repository\MFKDFPolicyRepository;
use App\State\UserMasterKeyDoubleHasher;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

//#[ApiResource(
//    operations: [
//        new Post(uriTemplate: '/crypto/policy', validationContext: ['groups' => ['Default', 'user:create']],
//            processor: UserMasterKeyDoubleHasher::class),
//    ],
//    normalizationContext: ['groups' => ['mfkdfpolicy:read']],
//    denormalizationContext: ['groups' => ['mfkdfpolicy:create', 'mfkdfpolicy:update']],
//)]
#[ORM\Entity(repositoryClass: MFKDFPolicyRepository::class)]
class MFKDFPolicy
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid|null $id = null;

    #[ORM\OneToOne(inversedBy: 'mfkdfpolicy', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $policyUser = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['mfkdfpolicy:read', 'mfkdfpolicy:create', 'mfkdfpolicy:update', 'user:create', 'user:update'])]
    private string $policy;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getPolicyUser(): ?User
    {
        return $this->policyUser;
    }

    public function setPolicyUser(User $policyUser): static
    {
        $this->policyUser = $policyUser;

        return $this;
    }

    public function getPolicy(): string
    {
        return $this->policy;
    }

    public function setPolicy(string $policy): static
    {
        $this->policy = $policy;

        return $this;
    }
}
