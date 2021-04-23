<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Image;
use App\Form\UserRegistrationType;
use App\Form\AnnouncerRegistrationType;
use App\Security\LoginFormAuthenticator;
use App\Service\ImageUploader;
use App\Service\DescriptionFilter;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/user-register", name="registrationUser")
     */
    public function registrationUser(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            if ($form->get('confirmPassword')->getData() !== $form->get('plainPassword')->getData()) {

                $form->get('confirmPassword')->addError(new FormError('Erreur dans la confirmation du mot de passe'));

                return $this->render('registration/user.html.twig', [
                    'formView' => $form->createView(),
                ]);
            }
            
            
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
            $this->addFlash('success', 'Inscription réussie !');

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
     * @Route("/announcer-register", name="registrationAnnouncer")
     */
    public function registrationAnnouncer(Request $request, UserPasswordEncoderInterface $passwordEncoder, ImageUploader $imageUploader, DescriptionFilter $descriptionFilter, EntityManagerInterface $em): Response
    {
        $user = new User();
        $image = new Image();
        $form = $this->createForm(AnnouncerRegistrationType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            // check desc

            $descriptionVerify = $descriptionFilter->filter($form->get('shortdescription')->getData());

            if ($descriptionVerify === true) {
                $form->get('shortdescription')->addError(new FormError("Votre description contient un ou plusieurs mots interdits"));
                return $this->render('registration/announcer.html.twig', [
                    'formView' => $form->createView()
                ]);
            }
            
            elseif ($form->get('confirmPassword')->getData() !== $form->get('plainPassword')->getData()) {

                $form->get('confirmPassword')->addError(new FormError('Erreur dans la confirmation du mot de passe'));

                return $this->render('registration/announcer.html.twig', [
                    'formView' => $form->createView(),
                ]);
            }



            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setAnnouncer('1');
            $user->setVip(new DateTime('now', new DateTimeZone('Europe/Paris')));

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();


            $imageFileName = $imageUploader->upload($imageFile);
            $image->setImageFilename($imageFileName);
            
            $user->addImage($image);

            $em->persist($user);
            $em->persist($image);

            $em->flush();
            // do anything else you need here, like send an email

            $this->addFlash('success', 'Inscription réussie ! Vous pouvez vous connectez mais un administrateur doit valider votre profil avant qu\'il soit affiché publiquement');
            return $this->redirectToRoute('home');
        }

        return $this->render('registration/announcer.html.twig', [
            'formView' => $form->createView()
        ]);
    }

}
