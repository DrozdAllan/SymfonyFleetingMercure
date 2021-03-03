<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\UserRepository;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(UserRepository $userRepository, ImageRepository $imageRepository)
    {
        $AssociatedImage = [];
        // ['id' => ['user' => objet utilisateur, 'imageFilename' => nom de l'image]]

        
        $criteria = ['validadmin' => '1' ];
        $users = $userRepository->findBy($criteria, ['id' => 'DESC'], 4);

            
            return $this->render('home.html.twig', [
                'users' => $users
        ]);
    }
}
