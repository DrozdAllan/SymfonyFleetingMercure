<?php

namespace App\Controller;

use App\Entity\Image;
use App\Service\ImageUploader;
use App\Form\UploadImageFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class ImageUploadController extends AbstractController
{
    /**
     * @Route("/profile/addimage", name="addimage")
     */
    public function addImage(Request $request, ImageUploader $imageUploader, EntityManagerInterface $em, UserInterface $userInterface)
    {

        $image = new Image();
        $form = $this->createForm(UploadImageFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {

                $user = $this->getUser();

                $image->setUser($user);
                $imageFileName = $imageUploader->upload($imageFile);
                $image->setImageFilename($imageFileName);
                
                $em->persist($image);
                $em->flush();
                $this->addFlash("success", "Image ajoutée avec succès");
            }

            return $this->redirectToRoute('home');
        }

        $formView = $form->createView();

        return $this->render('profile/addimage.html.twig', [
            'formView' => $formView
        ]);
    }
}
