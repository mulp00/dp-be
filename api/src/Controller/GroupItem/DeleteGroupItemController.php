<?php

namespace App\Controller\GroupItem;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\GroupItem;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class DeleteGroupItemController
{
    private Security $security;
    private EntityManagerInterface $entityManager;

    /**
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
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

        $itemId = $data['itemId'] ?? null;
        if (!$itemId) {
            throw new BadRequestHttpException('id is required');
        }



        /** @var GroupItem $groupItem */
        $groupItem = $iriConverter->getResourceFromIri('/group_items/' . $itemId);

        $group = $groupItem->getTargetGroup();

        if (!in_array($user, $group->getUsers()->toArray())) {
            throw new BadRequestHttpException('You cant access this resource');
        }

        $group->removeGroupItem($groupItem);

        $this->entityManager->persist($group);
        $this->entityManager->persist($groupItem);
        $this->entityManager->flush();


        return new Response(null, Response::HTTP_OK, ['Content-Type' => 'application/json']);


    }

}
