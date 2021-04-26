<?php

namespace App\Controller;

use DateTime;
use App\Service\VipValidation;
use App\Repository\UserRepository;
use App\Service\OfferDecision;
use Doctrine\ORM\EntityManagerInterface;
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
    public function offerverify($id, OfferDecision $offerDecision)
    {

        $userMail = $this->getUser()->getMail();

        $offerInfo = $offerDecision->OfferCalculator($id);


        \Stripe\Stripe::setApiKey($this->getParameter('StripeSecretKey'));

        $intent = \Stripe\PaymentIntent::create([
            'amount' => $offerInfo->VipPrice,
            'currency' => 'eur',
            'receipt_email' => $userMail,
        ]);

        $publickey = $this->getParameter('StripePublicKey');

        return $this->render('vip/checkout.html.twig', [
            'clientSecret' => $intent->client_secret,
            'offerInfo' => $offerInfo,
            'stripePublicKey' => $publickey
        ]);
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
    public function Webhook(UserRepository $userRepository, EntityManagerInterface $em, VipValidation $vipValidation)
    {
        \Stripe\Stripe::setApiKey('%env(STRIPE_SECRET_KEY)%');


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
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent

                $paidAmount = $paymentIntent->amount;
                // $customerMail = $paymentIntent->charges->data[0]->billing_details->email; POUR LE PREBUILD CHECKOUT
                $customerMail = $paymentIntent->receipt_email;

                $VipTime = $vipValidation->VipTimeCalculator($paidAmount);

                $user = $userRepository->findOneBy(['mail' => $customerMail]);

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
