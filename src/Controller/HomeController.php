<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResearchFormType;
use App\Repository\UserRepository;
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
        $user = new User;

        $criteria = ['validadmin' => '1'];
        $users = $userRepository->findBy($criteria, ['id' => 'DESC'], 8);

        $form = $this->createForm(ResearchFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $check = $form->getData();
            // dump($check);
            $hair = $user->getHair();
            // dump($hair);
            $tattoo = $user->getTattoo();
            // dump($tattoo);
            $smoke = $user->getSmoke();
            // dump($smoke);

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
                    'users' => $users,
                    'formView' => $form->createView()
                ]);
            }

            // dump($criteria);

            $results = $userRepository->findBy($criteria, ['id' => 'DESC']);

            // dd($results);
            return $this->render('search/advancedSearch.html.twig', [
                'announcers' => $results
            ]);
        }

        return $this->render('home.html.twig', [
            'users' => $users,
            'formView' => $form->createView()
        ]);
    }
}
