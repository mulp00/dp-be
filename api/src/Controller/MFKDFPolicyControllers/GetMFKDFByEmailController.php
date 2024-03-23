<?php

namespace App\Controller\MFKDFPolicyControllers;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\MFKDFPolicy;
use App\Repository\MFKDFPolicyRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


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


    public function __invoke(Request $request, IriConverterInterface $iriConverter): MFKDFPolicy
    {

        // Get text fields from form data
        $email = $request->get('id');

        // Basic validation for required fields
        if (!$email) {
            throw new BadRequestHttpException('Name is required');
        }

        $user = $this->userRepository->findOneBy(['email'=>$email]);

        dump($user);

        return $this->userRepository->findOneBy(['email'=>$email])->getMfkdfpolicy();
    }

}
