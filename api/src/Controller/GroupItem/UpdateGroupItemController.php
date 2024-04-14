<?php

namespace App\Controller\GroupItem;

use ApiPlatform\Api\IriConverterInterface;
use App\DTO\GroupItem\GroupItemDTO;
use App\Entity\Group;
use App\Entity\GroupItem;
use App\Entity\User;
use App\Enum\GroupItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class UpdateGroupItemController
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

    public function __invoke(Group $group, GroupItem $groupItem, Request $request, IriConverterInterface $iriConverter): Response
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

        $name = $data['name'] ?? null;
        if (!$name) {
            throw new BadRequestHttpException('name is required');
        }
        $description = $data['description'] ?? null;
        if (!$description) {
            throw new BadRequestHttpException('description is required');
        }

        $typeProvided = $data['type'] ?? null;
        if (!$typeProvided) {
            throw new BadRequestHttpException('type is required');
        }
        $content = $data['content'] ?? null;
        if (!$content) {
            throw new BadRequestHttpException('content is required');
        }
        $postedEpoch = $data['epoch'] ?? null;
        if (!$postedEpoch) {
            throw new BadRequestHttpException('epoch is required');
        }

        $type = GroupItemType::from($typeProvided);

        if (!in_array($user, $group->getUsers()->toArray())) {
            throw new BadRequestHttpException('You cant access this resource');
        }

        if($postedEpoch !== $group->getEpoch()){
            throw new BadRequestHttpException('Epochs dont match, catch up on group updates');
        }
        if($type !== $groupItem->getType()){
            throw new BadRequestHttpException('Item types do not match');
        }


        $groupItem->setContent($content);
        $groupItem->setName($name);
        $groupItem->setDescription($description);

        $this->entityManager->persist($group);
        $this->entityManager->persist($groupItem);
        $this->entityManager->flush();

        $jsonContentResponse = $this->serializer->serialize(new GroupItemDTO($groupItem), 'json');

        return new Response($jsonContentResponse, Response::HTTP_OK, ['Content-Type' => 'application/json']);


    }

}
