<?php

namespace App\Controller;

use DateTime;
use Psr\Log\LoggerInterface;
use App\Service\OfferDecision;
use App\Service\VipValidation;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class VipController extends AbstractController
{
    /**
     * @Route("/vip", name="vip")
     */
    public function vip()
    {

        return $this->render('vip/presentation.html.twig');
    }

    /**
     * @Route("/test", name="test")
     */
    public function test()
    {


        return $this->render('vip/test.html.twig');
    }

    /**
     * @Route("/vip/offer/{id}", name="offer")
     */
    public function offer($id, OfferDecision $offerDecision)
    {
        dump($id);

        // Connexion a notre compte stripe
        \Stripe\Stripe::setApiKey($this->getParameter('StripeSecretKey'));

        // Recup du customer dans notre db au travers de notre stripe
        $customerId = \Stripe\Customer::retrieve($this->getUser()->getStripe());

        $publickey = $this->getParameter('StripePublicKey');


        $session = \Stripe\Checkout\Session::create([
            'customer' => $customerId,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => 'price_1IkDZuDTsj5RSWQCOuyzExjS',
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('checkoutsuccess', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('checkoutcancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->render('vip/offer.html.twig', [
            'stripePublicKey' => $publickey,
            'id' => $session->id
        ]);
    }

    /**
     * @Route("/vip/subscribe", name="subscribe")
     */
    public function subscribe()
    {
        $stripePublicKey = $this->getParameter('StripePublicKey');

        $customerId = $this->getUser()->getStripe();

        \Stripe\Stripe::setApiKey($this->getParameter('StripeSecretKey'));

        try {
            $checkout_session = \Stripe\Checkout\Session::create([
                'customer' => $customerId,
                'payment_method_types' => ['card'],
                'mode' => 'subscription',
                'line_items' => [[
                    'price' => 'price_1IkDp4DTsj5RSWQCno9DR63U',
                    'quantity' => 1,
                ]],
                'success_url' => $this->generateUrl('checkoutsuccess', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('checkoutcancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }

        return $this->render('vip/subscribe.html.twig', [
            'stripePublicKey' => $stripePublicKey,
            'customerId' => $customerId,
            'sessionId' => $checkout_session->id
        ]);
    }

    /**
     * @Route("/webhook", name="webhook", methods={"POST"})
     */
    public function Webhook(LoggerInterface $logger, UserRepository $userRepository, EntityManagerInterface $em, VipValidation $vipValidation)
    {

        $payload = @file_get_contents('php://input');
        $event = null;

        try {
            $event = \Stripe\Event::constructFrom(
                json_decode($payload, true)
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        }

        // Handle the event
        switch ($event->type) {
                // Ecoute du seul event important
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object; // recup de l'objet \Stripe\PaymentIntent

                // Connexion a notre compte stripe
                \Stripe\Stripe::setApiKey($this->getParameter('StripeSecretKey'));



                // Recup de l'id du customer stripe et du montant qu'il vient de payer
                $customerId = $paymentIntent->customer;
                $paidAmount = $paymentIntent->amount;

                // verif si il sagit du montant de labonnement
                if ($paidAmount == 3490) {
                    // METTRE EN PLACE LA FONCTION DABONNEMENT DANS LA DB ET DANS SYMFONY (l'enfer)
                }

                // Utilisation du service pour calculer le temps vip a ajouter en fonction du montant payé
                $VipTime = $vipValidation->VipTimeCalculator($paidAmount);

                // Recup du mail du customer dont on reçoit l'event d'apres notre compte stripe pour le retrouver dans la db
                $retrievedCustomer = \Stripe\Customer::retrieve($customerId);
                $user = $userRepository->findOneBy(['mail' => $retrievedCustomer->email]);

                $userTime = $user->getVip();

                $now = new DateTime('now');

                if ($userTime < $now) {
                    // l'annonceur n'était pas ou plus vip
                    $AddedVipTime = $now->add($VipTime);
                    $user->setVip($AddedVipTime);
                } else {
                    // l'annonceur était déjà vip, on lui rajoute du temps
                    $MoreVipTime = $userTime->add($VipTime);
                    $user->setVip($MoreVipTime);
                }
                $em->flush();

                break;
            case 'checkout.session.completed':
                // Payment is successful and the subscription is created.
                // Creation du status vip pour un mois
                break;
            case 'invoice.paid':
                // Continue to provision the subscription as payments continue to be made.
                // Store the status in your database and check when a user accesses your service.
                // This approach helps you avoid hitting rate limits.
                // Rajout du statut vip pour un nouveau mois
                break;
            case 'invoice.payment_failed':
                // The payment failed or the customer does not have a valid payment method.
                // The subscription becomes past_due. Notify your customer and send them to the
                // customer portal to update their payment information.
                // Le paiement mensuel a échoué, envoyer notif à l'admin
                break;
                // ... handle other event types
            case 'customer.subscription.deleted':
                // L'abonnement d'un utilisateur vient d'être arrêté
                break;
                // ... handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
        }


        return new Response(200);
    }

    /**
     * @Route("/checkoutsuccess", name="checkoutsuccess")
     */
    public function checkoutsuccess()
    {

        return $this->render('vip/checkoutsuccess.html.twig');
    }

    /**
     * @Route("/checkoutcancel", name="checkoutcancel")
     */
    public function checkoutcancel()
    {

        return $this->render('vip/checkoutcancel.html.twig');
    }
}
