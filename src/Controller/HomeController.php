<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResearchType;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(UserRepository $userRepository, Request $request)
    {
        
        // prendre l'heure qu'il est pour voir qui est encore vip
        $time = new DateTime('now');


        $vips = $userRepository->findAnnouncersStillVip($time);


        $announcers = $userRepository->findAnnouncersNotVip($time);
        


        $user = new User;
        $form = $this->createForm(ResearchType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            //  mettre la fonction de recherche dans un service

            $check = $form->getData();
            $hair = $user->getHair();
            $tattoo = $user->getTattoo();
            $smoke = $user->getSmoke();

            if ($hair && $tattoo && $smoke) {
                $criteria = ['validadmin' => '1', 'hair' => $hair, 'tattoo' => $tattoo, 'smoke' => $smoke];
            } elseif ($hair && $tattoo) {
                $criteria = ['validadmin' => '1', 'hair' => $hair, 'tattoo' => $tattoo];
            } elseif ($tattoo && $smoke) {
                $criteria = ['validadmin' => '1', 'hair' => $hair, 'tattoo' => $tattoo];
            } elseif ($hair && $smoke) {
                $criteria = ['validadmin' => '1', 'hair' => $hair, 'tattoo' => $tattoo];
            } elseif ($hair) {
                $criteria = ['validadmin' => '1', 'hair' => $hair];
            } elseif ($tattoo) {
                $criteria = ['validadmin' => '1', 'tattoo' => $tattoo];
            } elseif ($smoke) {
                $criteria = ['validadmin' => '1', 'smoke' => $smoke];
            }
            else {
                $this->addFlash('danger', 'Vous devez choisir au moins un critère de recherche');
                return $this->render('home.html.twig', [
                    'users' => $announcers,
                    'formView' => $form->createView()
                ]);
            }

            $results = $userRepository->findBy($criteria, ['id' => 'DESC']);

            return $this->render('search/advancedSearch.html.twig', [
                'announcers' => $results
            ]);
        }

        return $this->render('home.html.twig', [
            'vips' => $vips,
            'announcers' => $announcers,
            'formView' => $form->createView()
        ]);
    }
}
