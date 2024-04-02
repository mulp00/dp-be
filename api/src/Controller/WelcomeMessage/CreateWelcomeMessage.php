<?php

namespace App\Controller\WelcomeMessage;

use ApiPlatform\Api\IriConverterInterface;
use App\DTO\SerializedUserGroupDTO;
use App\Entity\Group;
use App\Entity\SerializedUserGroup;
use App\Entity\User;
use App\Entity\WelcomeMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;


#[AsController]
class CreateWelcomeMessage
{
    private Security $security;
    private SerializerInterface $serializer;
    private EntityManagerInterface $entityManager;


    /**
     * @param Security $security
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(Security $security, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }


    public function __invoke(Request $request, IriConverterInterface $iriConverter): Response
    {
        dump('test');

        $user = $this->security->getUser();

        if (!$user) {
            throw new \RuntimeException('User must be authenticated to create this resource.');
        }

        if (!$user instanceof User) {
            throw new \RuntimeException('The authenticated user is not a valid \App\Entity\User instance.');
        }

        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('Invalid JSON body data');
        }

        $groupId = $data['groupId'] ?? null;
        if (!$groupId) {
            throw new BadRequestHttpException('groupId is required');
        }
        $memberId = $data['memberId'] ?? null;
        if (!$memberId) {
            throw new BadRequestHttpException('memberId is required');
        }
        $welcomeMessageString = $data['welcomeMessage'] ?? null;
        if (!$welcomeMessageString) {
            throw new BadRequestHttpException('welcomeMessage is required');
        }


        /** @var Group $group */
        $group = $iriConverter->getResourceFromIri('/groups/'.$groupId);
        /** @var User $member */
        $member = $iriConverter->getResourceFromIri('/users/'.$memberId);

        $welcomeMessage = new WelcomeMessage($user, $group, $welcomeMessageString);

        $this->entityManager->persist($welcomeMessage);
        $this->entityManager->flush();

        return new Response(null, Response::HTTP_OK, ['Content-Type' => 'application/json']);


    }
}
