<?php

namespace App\Controller;

use App\Form\UserModifyFormType;
use App\Form\ModifyAnnouncerFormType;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        $form = $this->createForm(ModifyAnnouncerFormType::class, $usermodify);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->flush();

            $this->addFlash("success", "Profil modifié avec succès");
            return $this->redirectToRoute('profile');
        }

        $formView = $form->createView();

        return $this->render('profile/modifyAnnouncerProfile.html.twig', [
            'formView' => $formView
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
        $usermodify = $this->getUser();
        $form = $this->createForm(UserModifyFormType::class, $usermodify);
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
}