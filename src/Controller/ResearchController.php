<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ResearchController extends AbstractController
{

    /**
     * @Route("/search", name="search")
     */
    public function simpleSearch(Request $request, UserRepository $userRepository)
    {
        $username = $request->query->get('username');

        $result = $userRepository->findAllConteningNameKey($username);
        
        dump($result);
       
        return $this->render('search/simpleSearch.html.twig', [
            'announcers' => $result
        ]);
    }
}
