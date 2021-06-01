<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResearchType;
use App\Repository\UserRepository;
use App\Service\AdvancedSearch;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(UserRepository $userRepository, Request $request, AdvancedSearch $advancedSearch)
    {
        
        // prendre l'heure qu'il est pour voir qui est encore vip
        $time = new DateTime('now');


        $vips = $userRepository->findAnnouncersStillVip($time);


        $announcers = $userRepository->findAnnouncersNotVip($time);
        


        $user = new User;
        $form = $this->createForm(ResearchType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $elements = (object) array('hair' => $user->getHair(),
            'tattoo' => $user->getTattoo(),
            'eyes' => $user->getEyes(),
            'nationality' => $user->getNationality(),
            'language' => $user->getLanguage(),
            'smoke' => $user->getSmoke());


            $criteria = $advancedSearch->find($elements);

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
