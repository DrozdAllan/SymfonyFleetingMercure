<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
