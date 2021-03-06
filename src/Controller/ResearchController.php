<?php

namespace App\Controller;

use App\Form\ResearchFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ResearchController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function simpleResearch(Request $request)
    {
        $form = $this->createForm(ResearchFormType::class);
        $form->handleRequest($request);
         
        if ($form->isSubmitted()) {
            $info = $request->request->all('research_form')['username'];
            // dd($info);
            

            $url = $this->generateUrl('viewAnnouncer', ['username' => $info]);

            dd($url);
            $this->redirect($url);
        }


        $formView = $form->createView();

            return $this->render('fragment/_simpleresearch.html.twig', [
                'formView' => $formView
        ]);
    }
}

