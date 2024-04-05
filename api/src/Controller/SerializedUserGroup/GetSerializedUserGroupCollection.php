<?php

namespace App\Controller\SerializedUserGroup;

use App\DTO\SerializedUserGroup\SerializedUserGroupCollectionDTO;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class GetSerializedUserGroupCollection
{
    private Security $security;
    private SerializerInterface $serializer;

    /**
     * @param Security $security
     * @param SerializerInterface $serializer
     */
    public function __construct(Security $security, SerializerInterface $serializer)
    {
        $this->security = $security;
        $this->serializer = $serializer;
    }


    public function __invoke(): Response
    {

        $user = $this->security->getUser();

        if (!$user) {
            throw new \RuntimeException('User must be authenticated to create this resource.');
        }

        if (!$user instanceof User) {
            throw new \RuntimeException('The authenticated user is not a valid \App\Entity\User instance.');
        }


        $serializedUserGroupCollection = new SerializedUserGroupCollectionDTO($user->getSerializedUserGroups()->toArray());


        $jsonContentResponse = $this->serializer->serialize($serializedUserGroupCollection->serializedUserGroups, 'json');


        return new Response($jsonContentResponse, Response::HTTP_OK, ['Content-Type' => 'application/json']);


    }

}
