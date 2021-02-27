<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function home(UserRepository $userRepository)
    {
        $criteria = ['validadmin' => '1' ];
        $users = $userRepository->findBy($criteria, ['id' => 'DESC'], 4);


        return $this->render('profile/profile.html.twig', [
            'users' => $users
        ]);
    }
}
