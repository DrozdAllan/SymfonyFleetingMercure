<?php

namespace App\Controller;


use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ViewAnnouncerController extends AbstractController
{
    /**
     * @Route("/announcer/{username}", name="viewAnnouncer")
     */
    public function viewAnnouncer($username, UserRepository $userRepository)
    {
        $criteria = ['username' => $username, 'validadmin' => 1];
        $announcer = $userRepository->findOneBy($criteria);

        
        return $this->render('announcerView/view.html.twig', [
            'announcer' => $announcer
        ]);
    }

}
