<?php

namespace App\Controller\WelcomeMessage;

use ApiPlatform\Api\IriConverterInterface;
use App\DTO\SerializedUserGroupDTO;
use App\Entity\Group;
use App\Entity\Message;
use App\Entity\SerializedUserGroup;
use App\Entity\User;
use App\Entity\WelcomeMessage;
use App\Repository\MessageRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
    private MessageRepository $messageRepository;

    /**
     * @param Security $security
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param MessageRepository $messageRepository
     */
    public function __construct(Security $security, SerializerInterface $serializer, EntityManagerInterface $entityManager, MessageRepository $messageRepository)
    {
        $this->security = $security;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->messageRepository = $messageRepository;
    }


    public function __invoke(Request $request, IriConverterInterface $iriConverter): Response
    {
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
        $memberId = $data['memberId'] ?? null;
        $welcomeMessageString = $data['welcomeMessage'] ?? null;

        if (!$groupId || !$memberId || !$welcomeMessageString) {
            throw new BadRequestHttpException('groupId, memberId and welcomeMessage are required');
        }

        /** @var Group $group */
        $group = $iriConverter->getResourceFromIri('/groups/'.$groupId);
        /** @var User $member */
        $member = $iriConverter->getResourceFromIri('/users/'.$memberId);

        $lastMessageEpochNumber = $this->messageRepository->findLatestMessageByGroupId($group->getId());

        try {
            $commitMessage = new Message($group, $lastMessageEpochNumber + 1);
            $welcomeMessage = new WelcomeMessage($member, $group, $welcomeMessageString, $commitMessage);
            $group->addUser($member);
            $this->entityManager->persist($commitMessage);
            $this->entityManager->persist($welcomeMessage);
            $this->entityManager->persist($member);
            $this->entityManager->persist($group);
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            // This exception is thrown if the unique constraint is violated
            throw new BadRequestHttpException('A welcome message for this combination already exists.');
        } catch (\Exception $e) {
            // Catch other exceptions to handle unexpected errors
            throw new BadRequestHttpException('An unexpected error occurred: ' . $e->getMessage());
        }


        return new Response(null, Response::HTTP_OK, ['Content-Type' => 'application/json']);


    }
}
