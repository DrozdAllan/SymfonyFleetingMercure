<?php

namespace App\Controller;

use DateTime;
use App\Service\VipValidation;
use App\Repository\UserRepository;
use App\Service\OfferDecision;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * @Route("/vip/checkout/{id}", name="checkout")
     */
    public function checkout($id, OfferDecision $offerDecision)
    {

        $userMail = $this->getUser()->getMail();

        $offerInfo = $offerDecision->OfferCalculator($id);


        \Stripe\Stripe::setApiKey($this->getParameter('StripeSecretKey'));

        $newCustomer = \Stripe\Customer::create([
            'email' => $userMail
        ]);

        $intent = \Stripe\PaymentIntent::create([
            'amount' => $offerInfo->VipPrice,
            'currency' => 'eur',
            'customer' => $newCustomer->id
        ]);

        $publickey = $this->getParameter('StripePublicKey');

        return $this->render('vip/checkout.html.twig', [
            'clientSecret' => $intent->client_secret,
            'offerInfo' => $offerInfo,
            'stripePublicKey' => $publickey
        ]);
    }

    /**
     * @Route("/vip/monthly", name="monthlysubscribe")
     */
    public function monthlySubscribe()
    {


        return $this->render('vip/monthlySubscribe.html.twig');
    }




    /**
     * @Route("/vip/checkoutsuccess", name="checkoutsuccess")
     */
    public function checkoutsuccess()
    {

        return $this->render('vip/checkoutsuccess.html.twig');
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
}
