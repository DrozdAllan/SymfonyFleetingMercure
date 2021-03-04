<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Image;
use App\Form\RegistrationFormType;
use App\Form\AnnouncerRegistrationType;
use App\Security\LoginFormAuthenticator;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/choice", name="register_choice")
     */
    public function choice()
    {

        return $this->render('registration/choice.html.twig');
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setAnnouncer('0');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/user.html.twig', [
            'formView' => $form->createView(),
        ]);
    }

    /**
     * @Route("/announcer", name="announcer_register")
     */
    public function Announcerregister(Request $request, UserPasswordEncoderInterface $passwordEncoder, ImageUploader $imageUploader, EntityManagerInterface $em): Response
    {
        $user = new User();
        $image = new Image();
        $form = $this->createForm(AnnouncerRegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setAnnouncer('1');

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();


            $imageFileName = $imageUploader->upload($imageFile);
            $image->setImageFilename($imageFileName);
            
            $user->addImage($image);

            $em->persist($user);
            $em->persist($image);

            $em->flush();
            // do anything else you need here, like send an email

            // Ajout d'un flash pour prévenir de la prise en charge Admin et redirect home
            $this->addFlash("success", "Inscription réussie ! Votre profil va être étudié pour validation");
            return $this->redirectToRoute('home');
            // return $guardHandler->authenticateUserAndHandleSuccess(
            //     $user,
            //     $request,
            //     $authenticator,
            //     'main' // firewall name in security.yaml
            // );
        }

        return $this->render('registration/announcer.html.twig', [
            'formView' => $form->createView()
        ]);
    }
}
