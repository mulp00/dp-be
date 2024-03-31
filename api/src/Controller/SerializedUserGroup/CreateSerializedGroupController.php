<?php

namespace App\Controller\SerializedUserGroup;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Group;
use App\Entity\SerializedUserGroup;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateSerializedGroupController
{
    private Security $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    public function __invoke(Request $request, IriConverterInterface $iriConverter): SerializedUserGroup
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if (!$user) {
            throw new \RuntimeException('User must be authenticated to create this resource.');
        }

        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('Invalid JSON body data');
        }

        $serializedGroup = $data['serializedGroup'] ?? null;
        if (!$serializedGroup) {
            throw new BadRequestHttpException('address is required');
        }

        $group = new Group($user);

        return new SerializedUserGroup($user, $serializedGroup, $group);

    }
}
