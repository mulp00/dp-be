<?php

namespace App\Controller\Group;

use App\DTO\WelcomeMessage\WelcomeMessageCollectionDTO;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class GetGroupsToJoin
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


    public function __invoke(Request $request): Response
    {

        $user = $this->security->getUser();

        if (!$user) {
            throw new \RuntimeException('User must be authenticated to access this resource.');
        }

        if (!$user instanceof User) {
            throw new \RuntimeException('The authenticated user is not a valid \App\Entity\User instance.');
        }

        $welcomeMessages = $user->getWelcomeMessages()->toArray();

        $usersGroups = $user->getGroups()->toArray();
        $groupsToJoin = [];


        foreach ($welcomeMessages as $welcomeMessage){
            // in case user was kicked before they could join then ignore the message
            if(in_array($welcomeMessage->getTargetGroup(), $usersGroups)) {
                if (!$welcomeMessage->isIsJoined()) {
                    $groupsToJoin[] = $welcomeMessage;
                }
            }
        }

        $welcomeMessageCollectionDTO = new WelcomeMessageCollectionDTO($groupsToJoin);

        $jsonContentResponse = $this->serializer->serialize($welcomeMessageCollectionDTO->welcomeMessages, 'json');

        return new Response($jsonContentResponse, Response::HTTP_OK, ['Content-Type' => 'application/json']);


    }


}
