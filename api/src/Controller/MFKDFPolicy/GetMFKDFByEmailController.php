<?php

namespace App\Controller\MFKDFPolicy;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\MFKDFPolicy;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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


    public function __invoke(Request $request, IriConverterInterface $iriConverter): MFKDFPolicy
    {

        // Get text fields from form data
        $email = $request->get('id');

        // Basic validation for required fields
        if (!$email) {
            throw new BadRequestHttpException('Name is required');
        }

	return $this->userRepository->findOneBy(['email'=>$email])->getMfkdfpolicy();
    }

}
