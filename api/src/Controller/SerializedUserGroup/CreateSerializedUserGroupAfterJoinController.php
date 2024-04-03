<?php

namespace App\Controller\SerializedUserGroup;

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
class CreateSerializedUserGroupAfterJoinController
{

    private Security $security;
    private EntityManagerInterface $entityManager;

    private SerializerInterface $serializer;

    /**
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     */
    public function __construct(Security $security, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
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
        if (!$groupId) {
            throw new BadRequestHttpException('groupId is required');
        }
        $serializedUserGroupContents = $data['serializedUserGroup'] ?? null;
        if (!$serializedUserGroupContents) {
            throw new BadRequestHttpException('serializedUserGroup is required');
        }

        $epoch = $data['epoch'] ?? null;
        if (!$epoch) {
            throw new BadRequestHttpException('epoch is required');
        }
        $welcomeMessageId = $data['welcomeMessageId'] ?? null;
        if (!$welcomeMessageId) {
            throw new BadRequestHttpException('welcomeMessageId is required');
        }

        /** @var Group $group */
        $group = $iriConverter->getResourceFromIri('/groups/' . $groupId);

        if (!in_array($user, $group->getUsers()->toArray())) {
            throw new BadRequestHttpException('You cant access this resource');
        }

        foreach ($user->getSerializedUserGroups() as $serializedUserGroup){
            if($serializedUserGroup->getGroupEntity() === $group){
                throw new BadRequestHttpException('Group already joined');
            }
        }

        $newSerializedUserGroup = new SerializedUserGroup($user, $serializedUserGroupContents, $group, $epoch);

        $this->entityManager->persist($newSerializedUserGroup);
        $this->entityManager->flush();

        $serializedUserGroupDTO = new SerializedUserGroupDTO($newSerializedUserGroup);

        $jsonContentResponse = $this->serializer->serialize($serializedUserGroupDTO, 'json');

        /** @var WelcomeMessage $welcomeMessage */
        $welcomeMessage = $iriConverter->getResourceFromIri('/welcome_messages/' . $welcomeMessageId);
        $welcomeMessage->setIsJoined(true);

        $this->entityManager->persist($group);
        $this->entityManager->persist($user);
        $this->entityManager->persist($welcomeMessage);
        $this->entityManager->flush();

        return new Response($jsonContentResponse, Response::HTTP_OK, ['Content-Type' => 'application/json']);


    }


}
