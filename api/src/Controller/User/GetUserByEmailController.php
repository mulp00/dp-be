<?php

namespace App\Controller\User;

use App\DTO\PublicUserDTO;
use App\DTO\PublicUserGroupDTO;
use App\DTO\SerializedUserGroupDTO;
use App\Entity\SerializedUserGroup;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;


#[AsController]
class GetUserByEmailController
{
    private Security $security;
    private SerializerInterface $serializer;
    private UserRepository $userRepository;

    /**
     * @param Security $security
     * @param SerializerInterface $serializer
     * @param UserRepository $userRepository
     */
    public function __construct(Security $security, SerializerInterface $serializer, UserRepository $userRepository)
    {
        $this->security = $security;
        $this->serializer = $serializer;
        $this->userRepository = $userRepository;
    }


    public function __invoke(Request $request): Response
    {

        $user = $this->security->getUser();

        if (!$user) {
            throw new \RuntimeException('User must be authenticated to access this resource.');
        }

        if (!$user instanceof User) {
            throw new \RuntimeException('The authenticated user is not a valid \App\Entity\User instance.');
        }

        $searchedEmail = $request->get('id');

        $searchResult = $this->userRepository->findByPartialEmail($searchedEmail, 30);

        $foundUsers = (new PublicUserGroupDTO($searchResult))->getUsers();

        $jsonContentResponse = $this->serializer->serialize($foundUsers, 'json');

        return new Response($jsonContentResponse, Response::HTTP_OK, ['Content-Type' => 'application/json']);


    }

}
