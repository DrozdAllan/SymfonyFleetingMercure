<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Image;
use App\Form\UploadImageType;
use App\Service\ImageUploader;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImageController extends AbstractController
{
    /**
     * @Route("/profile/addimage", name="addImage")
     */
    public function addImage(Request $request, ImageUploader $imageUploader, EntityManagerInterface $em)
    {

        $image = new Image;
        $user = $this->getUser();

        $form = $this->createForm(UploadImageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {

                /** @var User $user */
                $imageFileName = $imageUploader->upload($imageFile);
                $image->setImageFilename($imageFileName);

                $user->addImage($image);
                $em->persist($image);
                $em->flush();
                $this->addFlash("success", "Image ajoutée avec succès");
            }

            return $this->redirectToRoute('profile');
        }

        $formView = $form->createView();

        return $this->render('profile/addImage.html.twig', [
            'formView' => $formView
        ]);
    }


    /**
     * @Route("/profile/deleteimage/{imageid}", name="announcerDeleteImage")
     * Apparemment pas safe
     */
    public function announcerDeleteImage($imageid, ImageUploader $imageUploader, ImageRepository $imageRepository, EntityManagerInterface $em)
    {
        $image = $imageRepository->findOneBy(['id' => $imageid]);

        if ($image) {

            // Supprimer image du dossier Public/Uploads
           $imageUploader->delete($image);

            // Supprimer image de la DB
            $em->remove($image);
            $em->flush();
            $this->addFlash("success", "Image supprimée avec succès");

            //redirection vers le profil de l'announcer
            return $this->redirectToRoute('modifyAnnouncerProfile');
        }
    }
}
