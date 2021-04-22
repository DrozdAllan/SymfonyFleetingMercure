<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserModifyType;
use App\Form\ModifyAnnouncerType;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function profile()
    {
        return $this->render('profile/profile.html.twig');
    }

    /**
     * @Route("/profile/modify-announcer", name="modifyAnnouncerProfile")
     */
    public function modifyAnnouncerProfile(Request $request, EntityManagerInterface $em)
    {
        $usermodify = $this->getUser();
        // dd($usermodify);
        $form = $this->createForm(ModifyAnnouncerType::class, $usermodify);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em->flush();
            
            $this->addFlash("success", "Profil modifié avec succès");
            return $this->redirectToRoute('profile');
        }

        return $this->render('profile/modifyAnnouncerProfile.html.twig', [
            'formView' => $form->createView()
        ]);
    }

     /**
     * @Route("/profile/deleteimage/{imageid}", name="announcerDeleteImage")
     * Apparemment pas safe mais ballec
     */
    public function announcerDeleteImage($imageid, Request $request, ImageRepository $imageRepository, EntityManagerInterface $em)
    {
        $image = $imageRepository->findOneBy(['id' => $imageid]);

        if ($image) {

            // Supprimer image du dossier Public/Uploads
            $nom = $image->getImageFilename();

            $filePath = "uploads\\images\\" . $nom;
            // dd($filePath);
            unlink($filePath);

            // Supprimer image de la DB
            $em->remove($image);
            $em->flush();
            // $this->addFlash("success", "Image ajoutée avec succès");

            //redirection vers le profil de l'announcer
            return $this->redirectToRoute('modifyAnnouncerProfile');
        }

    }

    /**
     * @Route("/profile/modifyuserprofile", name="modifyUserProfile")
     */
    public function modifyUserProfile(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserModifyType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->flush();

            $this->addFlash("success", "Profil modifié avec succès");
            return $this->redirectToRoute('profile');
        }

        $formView = $form->createView();

        return $this->render('profile/modifyUserProfile.html.twig', [
            'formView' => $formView
        ]);
    }
    /**
     * @Route("/profile/modifypassword", name="modifyPassword")
     */
    public function modifyPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            //Recup ancien mot de passe
            $oldPassword = $request->request->get('change_password_form')['oldPassword'];

            //Recup nouveau mot de passe
            $newPassword = $request->request->get('change_password_form')['plainPassword']['second'];

            //Si l'ancien mdp correspond
            if ($passwordEncoder->isPasswordValid($user, $oldPassword)) {
            //Alors encodage new mdp
            $newPassword = $passwordEncoder->encodePassword($user, $newPassword);
            $user->setPassword($newPassword);

            $em->flush();
            $this->addFlash("success", "Mot de passe modifié avec succès");
            return $this->redirectToRoute('profile');
            }
            else {
           $this->addFlash("danger", "Erreur dans le mot de passe actuel");
            }
        }

        return $this->render('profile/modifypassword.html.twig', [
            'passwordFormView' => $form->createView()
        ]);
    }

     /**
     * @Route("/profile/modifyuserprofile/accounttermination", name="accountTermination")
     */
    public function accountTermination(EntityManagerInterface $em)
    {
        $userAccountToDelete = $this->getUser();

        if ($userAccountToDelete == "caca") {
            $em->flush();

            $this->addFlash("success", "Profil modifié avec succès");
            return $this->redirectToRoute('profile');
        }



        return $this->render('profile/modifyUserProfile.html.twig');
    }
}
