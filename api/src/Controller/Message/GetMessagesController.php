<?php

namespace App\Controller\Message;

use ApiPlatform\Api\IriConverterInterface;
use App\DTO\MessageCollectionDTO;
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
class GetMessagesController
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
            throw new BadRequestHttpException('address is required');
        }
        $epoch = $data['epoch'] ?? null;
        if (!$epoch) {
            throw new BadRequestHttpException('address is required');
        }
        /** @var Group $group */
        $group = $iriConverter->getResourceFromIri('/groups/' . $groupId);

        $messages = $this->messageRepository->findMessagesByGroupIdWithMinEpoch($group->getId(), $epoch);

        $messagesDTO = new MessageCollectionDTO($messages);

        $jsonContentResponse = $this->serializer->serialize($messagesDTO, 'json');


        return new Response($jsonContentResponse, Response::HTTP_OK, ['Content-Type' => 'application/json']);


    }

}
