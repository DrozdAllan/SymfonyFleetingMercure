<?php

namespace App\Controller;

use DateTime;
use Psr\Log\LoggerInterface;
use App\Service\OfferDecision;
use App\Service\VipValidation;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/vip/test", name="test")
     */
    public function test()
    {
        // Connexion a notre compte stripe
        // \Stripe\Stripe::setApiKey($this->getParameter('StripeSecretKey'));

        // dd($checkout_session);

        return $this->render('vip/checkouttest');
    }

    /**
     * @Route("/vip/checkout/{id}", name="checkout")
     */
    public function checkout($id, OfferDecision $offerDecision)
    {

        $offerInfo = $offerDecision->OfferCalculator($id);
        \Stripe\Stripe::setApiKey($this->getParameter('StripeSecretKey'));
        $StripeCustomer = \Stripe\Customer::retrieve($this->getUser()->getStripe());

        $intent = \Stripe\PaymentIntent::create([
            'amount' => $offerInfo->VipPrice,
            'currency' => 'eur',
            'customer' => $StripeCustomer->id
        ]);

        $publickey = $this->getParameter('StripePublicKey');

        return $this->render('vip/checkout.html.twig', [
            'clientSecret' => $intent->client_secret,
            'offerInfo' => $offerInfo,
            'stripePublicKey' => $publickey
        ]);
    }

    /**
     * @Route("/vip/subscribe", name="subscribe")
     */
    public function subscribe()
    {
        $stripePublicKey = $this->getParameter('StripePublicKey');

        $customerId = $this->getUser()->getStripe();

        return $this->render('vip/subscribe.html.twig', [
            'stripePublicKey' => $stripePublicKey,
            'customerId' => $customerId
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
}
