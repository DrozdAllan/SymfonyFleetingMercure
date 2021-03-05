<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(UserRepository $userRepository)
    {
        $criteria = ['validadmin' => '1' ];
        $users = $userRepository->findBy($criteria, ['id' => 'DESC'], 4);
            
            return $this->render('home.html.twig', [
                'users' => $users
        ]);
    }
}
