<?php

namespace App\Controller\GroupItem;

use ApiPlatform\Api\IriConverterInterface;
use App\DTO\GroupItem\GroupItemCollectionDTO;
use App\DTO\Message\MessageCollectionDTO;
use App\Entity\Group;
use App\Entity\User;
use App\Repository\GroupItemRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
#[AsController]
class GetGroupItemCollectionController
{
    private GroupItemRepository $groupItemRepository;
    private SerializerInterface $serializer;
    private Security $security;

    /**
     * @param GroupItemRepository $groupItemRepository
     * @param SerializerInterface $serializer
     * @param Security $security
     */
    public function __construct(GroupItemRepository $groupItemRepository, SerializerInterface $serializer, Security $security)
    {
        $this->groupItemRepository = $groupItemRepository;
        $this->serializer = $serializer;
        $this->security = $security;
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

        if (!in_array($user, $group->getUsers()->toArray())) {
            throw new BadRequestHttpException('You cant access this resource');
        }

        $groupItems = $this->groupItemRepository->findByTargetGroupId($group->getId());

        $groupItemsDto = new GroupItemCollectionDTO($groupItems);

        $jsonContentResponse = $this->serializer->serialize($groupItemsDto->groupItems, 'json');


        return new Response($jsonContentResponse, Response::HTTP_OK, ['Content-Type' => 'application/json']);


    }

}
