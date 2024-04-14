<?php

namespace App\Controller\Group;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Group;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class DeleteGroupController
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

        if($user !== $group->getCreator()){
            throw new BadRequestHttpException('Only the groups creator can delete it');
        }


        try {

            foreach ($group->getUsers() as $groupUser){
                $serializedUserGroupToRemove = $groupUser->getSerializedUserGroupByGroup($group);
                if($serializedUserGroupToRemove !== null){
                    $groupUser->removeSerializedUserGroup($serializedUserGroupToRemove);
                    $this->entityManager->persist($groupUser);
                }
            }

            $this->entityManager->remove($group);
            $this->entityManager->flush();

        } catch (\Exception $e) {
            // Catch other exceptions to handle unexpected errors
            throw new BadRequestHttpException('An unexpected error occurred: ' . $e->getMessage());
        }

        return new Response(null, Response::HTTP_OK, ['Content-Type' => 'application/json']);

    }
}
