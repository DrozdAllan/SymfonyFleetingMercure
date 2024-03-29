<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserModifyType;
use App\Service\ImageUploader;
use App\Form\ChangePasswordType;
use App\Form\ModifyAnnouncerType;
use App\Repository\UserRepository;
use App\Repository\ImageRepository;
use App\Form\ProfileTerminationType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use SymfonyCasts\Bundle\ResetPassword\Command\ResetPasswordRemoveExpiredCommand;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

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
     * @Route("/stripeprofile", name="stripeprofile")
     */
    public function stripeprofile()
    {
        \Stripe\Stripe::setApiKey($this->getParameter('StripeSecretKey'));

        $customerId = $this->getUser()->getStripe();
        // Authenticate your user.
        $session = \Stripe\BillingPortal\Session::create([
            'customer' => $customerId,
            'return_url' => $this->generateUrl('profile', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        // Redirect to the customer portal.
        header("Location: " . $session->url);
        exit();
    }

    /**
     * @Route("/profile/modifyannouncer", name="modifyAnnouncerProfile")
     */
    public function modifyAnnouncerProfile(Request $request, EntityManagerInterface $em)
    {
        $usermodify = $this->getUser();
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
        $form = $this->createForm(ChangePasswordType::class, $user);

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
            } else {
                $this->addFlash("danger", "Erreur dans le mot de passe actuel");
            }
        }

        return $this->render('profile/modifyPassword.html.twig', [
            'passwordFormView' => $form->createView()
        ]);
    }

    /**
     * @Route("/profile/accounttermination", name="accountTermination")
     */
    public function accountTermination(ResetPasswordHelperInterface $resetPasswordHelperInterface, SessionInterface $session, TokenStorageInterface $tokenStorage, ImageUploader $imageUploader, Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {

        $user = $this->getUser();

        $form = $this->createForm(ProfileTerminationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //Recup mot de passe
            $password = $request->request->get('profile_termination')['plainPassword']['second'];

            //Si mdp correspond
            if ($passwordEncoder->isPasswordValid($user, $password)) {

                //Alors suppression images PHYSIQUEMENT et sur la db

                /** @var User $user */
                $userImages = $user->getImages();

                foreach ($userImages as $image) {
                    $user->removeImage($image);
                    $imageUploader->delete($image);
                }

                // Suppression des channels concernés par l'user
                $channels = $user->getChannels();

                foreach ($channels as $channel) {
                    $em->remove($channel);
                }



                // Puis suppression du reste
                $em->remove($user);
                $em->flush();


                //Suppression du token authentifié pour retour home sans erreur d'authentification
                $tokenStorage->setToken(null);
                //Invalidation session pour ne plus avoir le pseudo si tentative reco direct A TESTER
                $session->invalidate(0);

                $this->addFlash("success", "Profil supprimé avec succès");
                return $this->redirectToRoute('home');
            } else {
                $this->addFlash("danger", "Mauvais mot de passe");
            }
        }

        return $this->render('profile/accountTermination.html.twig', [
            'formView' => $form->createView()
        ]);
    }
}
