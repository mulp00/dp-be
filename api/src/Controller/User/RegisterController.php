<?php

namespace App\Controller\User;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\MFKDFPolicy;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class RegisterController
{
    private readonly UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;

    private SerializerInterface $serializer;

    /**
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    public function __invoke(Request $request, IriConverterInterface $iriConverter): Response
    {

        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('Invalid JSON body data');
        }

        $email = $data['email'] ?? null;
        if (!$email) {
            throw new BadRequestHttpException('email is required');
        }
        $masterKey = $data['masterKey'] ?? null;
        if (!$masterKey) {
            throw new BadRequestHttpException('masterKey is required');
        }
        $postedMfkdfpolicy = $data['mfkdfpolicy'] ?? null;
        if (!$postedMfkdfpolicy) {
            throw new BadRequestHttpException('mfkdfpolicy is required');
        }
        $serializedIdentity = $data['serializedIdentity'] ?? null;
        if (!$serializedIdentity) {
            throw new BadRequestHttpException('serializedIdentity is required');
        }
        $keyPackage = $data['keyPackage'] ?? null;
        if (!$keyPackage) {
            throw new BadRequestHttpException('keyPackage is required');
        }
        $keyStore = $data['keyStore'] ?? null;
        if (!$keyStore) {
            throw new BadRequestHttpException('keyStore is required');
        }

        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {

            return new Response(
                $this->serializer->serialize([
                'error' => 'User with this email already exists.',
            ], 'json'),
                Response::HTTP_CONFLICT,
                ['Content-Type' => 'application/json']
            );
        }

        $user = new User();

        $user->setEmail($email);
        $masterKeyHash = $this->passwordHasher->hashPassword($user, $masterKey);

        $user->setMasterKeyHash($masterKeyHash);

        $user->setMfkdfpolicy($postedMfkdfpolicy);
        $user->setSerializedIdentity($serializedIdentity);
        $user->setKeyPackage($keyPackage);
        $user->setKeyStore($keyStore);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response(null, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

}
