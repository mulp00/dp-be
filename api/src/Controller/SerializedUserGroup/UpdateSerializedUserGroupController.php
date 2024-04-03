<?php

namespace App\Controller\SerializedUserGroup;

use ApiPlatform\Api\IriConverterInterface;
use App\DTO\SerializedUserGroupDTO;
use App\Entity\SerializedUserGroup;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class UpdateSerializedUserGroupController
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

        $serializedUserGroupId = $data['serializedUserGroupId'] ?? null;
        if (!$serializedUserGroupId) {
            throw new BadRequestHttpException('serializedUserGroupId is required');
        }
        $serializedUserGroupContents = $data['serializedUserGroup'] ?? null;
        if (!$serializedUserGroupContents) {
            throw new BadRequestHttpException('serializedUserGroup is required');
        }
        $epoch = $data['epoch'] ?? null;
        if (!$epoch) {
            throw new BadRequestHttpException('epoch is required');
        }

        /** @var SerializedUserGroup $serializedUserGroup */
        $serializedUserGroup = $iriConverter->getResourceFromIri('/serialized_user_groups/' . $serializedUserGroupId);

        if (!$serializedUserGroup->getGroupUser() == $user) {
            throw new BadRequestHttpException('You cant access this resource');
        }

        $serializedUserGroup->setSerializedGroup($serializedUserGroupContents);
        $serializedUserGroup->setLastEpoch($epoch);

        $this->entityManager->persist($serializedUserGroup);
        $this->entityManager->flush();

        $serializedUserGroupDTO = new SerializedUserGroupDTO($serializedUserGroup);

        $jsonContentResponse = $this->serializer->serialize($serializedUserGroupDTO, 'json');

        return new Response($jsonContentResponse, Response::HTTP_OK, ['Content-Type' => 'application/json']);


    }
}
