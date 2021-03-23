<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResearchFormType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ResearchController extends AbstractController
{

    /**
     * @Route("/search", name="simpleSearch")
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
