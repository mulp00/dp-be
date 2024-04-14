<?php

namespace App\Controller\Group;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Group;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class RemoveUserFromGroupController
{
    private Security $security;
    private EntityManagerInterface $entityManager;
    private MessageRepository $messageRepository;

    /**
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     * @param MessageRepository $messageRepository
     */
    public function __construct(Security $security, EntityManagerInterface $entityManager, MessageRepository $messageRepository)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->messageRepository = $messageRepository;
    }


    public function __invoke(Group $group, Request $request, IriConverterInterface $iriConverter): Response
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

        $message = $data['message'] ?? null;
        if (!$message) {
            throw new BadRequestHttpException('message is required');
        }

        $targetUserId = $data['userId'] ?? null;
        if (!$targetUserId) {
            throw new BadRequestHttpException('targetUserId is required');
        }
        $postedEpoch = $data['epoch'] ?? null;
        if (!$postedEpoch) {
            throw new BadRequestHttpException('postedEpoch is required');
        }

        /** @var User $removedUser */
        $removedUser = $iriConverter->getResourceFromIri('/users/'.$targetUserId);

        if (!in_array($user, $group->getUsers()->toArray())) {
            throw new BadRequestHttpException('You cant access this resource');
        }

        $latestMessage = $this->messageRepository->findLatestMessageByGroupId($group->getId());
        $lastMessageEpochNumber = $latestMessage ? $latestMessage->getEpoch() : 1;

        if ($postedEpoch !== $lastMessageEpochNumber) {
            throw new BadRequestHttpException('Invalid epoch');
        }

        try {

            $removedUserSerializedUserGroup = $removedUser->getSerializedUserGroupByGroup($group);
            if($removedUserSerializedUserGroup !== null) {
                $removedUser->removeSerializedUserGroup($removedUserSerializedUserGroup);
            }
            $removedUser->removeGroup($group);

            $commitMessage = new Message($group, $postedEpoch + 1, $message);
            $group->addEpoch();
            $group->removeUser($removedUser);

            $this->entityManager->persist($commitMessage);
            $this->entityManager->persist($group);
            $this->entityManager->persist($removedUser);
            $this->entityManager->flush();

        } catch (\Exception $e) {
            // Catch other exceptions to handle unexpected errors
            throw new BadRequestHttpException('An unexpected error occurred: ' . $e->getMessage());
        }

        return new Response(null, Response::HTTP_OK, ['Content-Type' => 'application/json']);

    }
}
