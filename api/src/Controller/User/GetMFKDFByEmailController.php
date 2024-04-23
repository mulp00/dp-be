<?php

namespace App\Controller\User;

use ApiPlatform\Api\IriConverterInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Response;

#[AsController]
class GetMFKDFByEmailController extends AbstractController
{

    private UserRepository $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function __invoke(Request $request, IriConverterInterface $iriConverter): Response
    {

        // Get text fields from form data
        $email = $request->get('id');

        // Basic validation for required fields
        if (!$email) {
            throw new BadRequestHttpException('Email is required');
        }

        $mkdfpolicy = $this->userRepository->findOneBy(['email' => $email])->getMfkdfpolicy();

        return new Response($mkdfpolicy, Response::HTTP_OK, ['Content-Type' => 'application/json']);


    }

}
