<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function home()
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/announcers", name="announceradmin")
     */
    public function announcersadmin(UserRepository $userRepository)
    {
     
        $criteria = ['Announcer' => '1', 'validadmin' => null ];
        $waitingAnnouncers = $userRepository->findBy($criteria, ['id' => 'ASC']);
        
        return $this->render('admin/announceradmin.html.twig', [
            'waitingAnnouncers' => $waitingAnnouncers
        ]);
    }

    /**
     * @Route("/admin/announcers/validate?{id}", name="announcervalidate")
     */
    public function announcervalidate($id, UserRepository $userRepository, EntityManagerInterface $em) {
        //1 Récupérer l'id validé
        $announcer = $userRepository->find($id);
        //2 Changer le statut validadmin de null à 1 avec l'entity manager
        $announcer->setValidadmin('1');
        $em->flush();
        //3 Retour à la même page
        return $this->redirectToRoute('announceradmin');
    }
}
