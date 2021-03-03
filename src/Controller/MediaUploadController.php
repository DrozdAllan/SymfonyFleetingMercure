<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\User;
use App\Form\UploadMediaFormType;
use App\Repository\MediaRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MediaUploadController extends AbstractController
{
    /**
     * @Route("/profile/addimage", name="addimage")
     */
    public function addImage(Request $request, EntityManagerInterface $em)
    {
        $media = new Media;
        
        $form = $this->createForm(UploadMediaFormType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $em->persist($media);
            $em->flush();

            $this->addFlash("success", "Image ajoutée avec succès");
            return $this->redirectToRoute('home');
        }

        $formView = $form->createView();

        return $this->render('profile/addimage.html.twig', [
            'formView' => $formView
        ]);
    }
}
