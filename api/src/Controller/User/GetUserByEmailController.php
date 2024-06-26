<?php

namespace App\Controller\User;


use App\DTO\User\PublicUserCollectionDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\SerializerInterface;


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

        $searchedEmail = $request->query->get('email');

        $searchResult = $this->userRepository->findByPartialEmail($searchedEmail, 30);

        $foundUsers = (new PublicUserCollectionDTO($searchResult))->getUsers();

        $jsonContentResponse = $this->serializer->serialize($foundUsers, 'json');

        return new Response($jsonContentResponse, Response::HTTP_OK, ['Content-Type' => 'application/json']);


    }

}
