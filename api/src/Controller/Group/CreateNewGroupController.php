<?php

namespace App\Controller\Group;

use ApiPlatform\Api\IriConverterInterface;
use App\DTO\SerializedUserGroupDTO;
use App\Entity\Group;
use App\Entity\SerializedUserGroup;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class CreateNewGroupController
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

        $serializedGroup = $data['serializedGroup'] ?? null;
        if (!$serializedGroup) {
            throw new BadRequestHttpException('serializedGroup is required');
        }
        $groupName = $data['name'] ?? null;
        if (!$groupName) {
            throw new BadRequestHttpException('name is required');
        }
        $ratchetTree = $data['ratchetTree'] ?? null;
        if (!$ratchetTree) {
            throw new BadRequestHttpException('ratchetTree is required');
        }

        $group = new Group($groupName, $user, $ratchetTree);

        $serializedUserGroup = new SerializedUserGroup($user, $serializedGroup, $group, 1);

        $this->entityManager->persist($group);
        $this->entityManager->persist($serializedUserGroup);
        $this->entityManager->flush();

        $jsonContentResponse = $this->serializer->serialize(new SerializedUserGroupDTO($serializedUserGroup), 'json');


        return new Response($jsonContentResponse, Response::HTTP_OK, ['Content-Type' => 'application/json']);


    }
}
