<?php

namespace App\Controller\Message;

use ApiPlatform\Api\IriConverterInterface;
use App\DTO\Message\MessageCollectionDTO;
use App\Entity\Group;
use App\Entity\User;
use App\Repository\MessageRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class GetMessagesCollectionController
{
    private Security $security;
    private SerializerInterface $serializer;

    private MessageRepository $messageRepository;

    /**
     * @param Security $security
     * @param SerializerInterface $serializer
     * @param MessageRepository $messageRepository
     */
    public function __construct(Security $security, SerializerInterface $serializer, MessageRepository $messageRepository)
    {
        $this->security = $security;
        $this->serializer = $serializer;
        $this->messageRepository = $messageRepository;
    }


    public function __invoke(Group $group, Request $request, IriConverterInterface $iriConverter): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new \RuntimeException('User must be authenticated to access this resource.');
        }

        if (!$user instanceof User) {
            throw new \RuntimeException('The authenticated user is not a valid \App\Entity\User instance.');
        }

        $epoch = $request->query->get('epoch');
        if (!$epoch) {
            throw new BadRequestHttpException('epoch is required');
        }

        if (!in_array($user, $group->getUsers()->toArray())) {
            throw new BadRequestHttpException('You cannot access this resource');
        }

        $messages = $this->messageRepository->findMessagesByGroupIdWithMinEpoch($group->getId(), $epoch);
        $messagesCollectionDTO = new MessageCollectionDTO($messages);
        $jsonContentResponse = $this->serializer->serialize($messagesCollectionDTO->messages, 'json');

        return new Response($jsonContentResponse, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

}
