<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserModifyFormType;
use App\Repository\UserRepository;
use App\Form\AnnouncerModifyFormType;
use App\Form\AnnouncerRegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User as UserUser;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function profilehome()
    {

        
        return $this->render('profile/profile.html.twig');
    }

    /**
     * @Route("/profile/modifyannouncerprofile", name="modifyannouncerprofile")
     */
    public function modifyAnnouncerProfile(Request $request, EntityManagerInterface $em)
    {
        $usermodify = $this->getUser();
        // dd($usermodify);
        $form = $this->createForm(AnnouncerModifyFormType::class, $usermodify);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->flush();

            $this->addFlash("success", "Profil modifié avec succès");
            return $this->redirectToRoute('profile');
        }

        $formView = $form->createView();

        return $this->render('profile/modifyannouncerprofile.html.twig', [
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/profile/modifyuserprofile", name="modifyuserprofile")
     */
    public function modifyUserProfile(Request $request, EntityManagerInterface $em)
    {
        $usermodify = $this->getUser();
        $form = $this->createForm(UserModifyFormType::class, $usermodify);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->flush();

            $this->addFlash("success", "Profil modifié avec succès");
            return $this->redirectToRoute('profile');
        }

        $formView = $form->createView();

        return $this->render('profile/modifyuserprofile.html.twig', [
            'formView' => $formView
        ]);
    }
}
