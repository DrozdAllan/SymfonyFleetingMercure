<?php

namespace App\Controller;

use App\Form\ResearchFormType;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(UserRepository $userRepository, Request $request)
    {
        $criteria = ['validadmin' => '1' ];
        $users = $userRepository->findBy($criteria, ['id' => 'DESC'], 8);

        $form = $this->createForm(ResearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {


            $this->redirectToRoute('advancedSearch');
        }

            
            return $this->render('home.html.twig', [
                'users' => $users,
                'formView' => $form->createView()
        ]);
    }
}
