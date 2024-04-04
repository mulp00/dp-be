<?php

namespace App\Controller\User;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Group;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class UpdateKeyPackageController
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

        $keyPackage = $data['keyPackage'] ?? null;
        if (!$keyPackage) {
            throw new BadRequestHttpException('keyPackage is required');
        }

        try {

            $user->setKeyPackage($keyPackage);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

        } catch (\Exception $e) {
            // Catch other exceptions to handle unexpected errors
            throw new BadRequestHttpException('An unexpected error occurred: ' . $e->getMessage());
        }

        return new Response(null, Response::HTTP_OK, ['Content-Type' => 'application/json']);

    }
}
