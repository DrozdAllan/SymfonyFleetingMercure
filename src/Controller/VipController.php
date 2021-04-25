<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Service\VipValidation;
use App\Repository\UserRepository;
use App\Service\OfferDecision;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VipController extends AbstractController
{
    /**
     * @Route("/vip/test", name="viptest")
     */
    public function viptest(VipValidation $vipValidation, UserRepository $userRepository, EntityManagerInterface $em)
    {
        // Remplacer cette fonction par le système de mise en place du status VIP en fonction du paiement effectué (sûrement un service)
        $paidAmount = 1800;

        // $customerMail = $paymentIntent->charges->data[0]->billing_details->email; POUR LE PREBUILD CHECKOUT
        $customerMail = 'jean@gmail.com';

        $VipTime = $vipValidation->VipTimeCalculator($paidAmount);

        $user = $userRepository->findOneBy(['mail' => $customerMail]);

        $userTime = $user->getVip();
        // 3 JOURS

        
        $now = new DateTime('now');
        
        dump($userTime, $now);

        if ($userTime < $now) {
            dump('inferieur');
            $AddedVipTime = $now->add($VipTime);
            $user->setVip($AddedVipTime);
        } else {
            dump('superieur');
            $MoreVipTime = $userTime->add($VipTime);
            $user->setVip($MoreVipTime);
        }

        dd($user);
        $em->flush();

        return $this->render('vip/test.html.twig');
    }


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

        // dd($intent);

        return $this->render('vip/checkout.html.twig', [
            'clientSecret' => $intent->client_secret,
            'offerInfo' => $offerInfo
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
     * @Route("/vip/checkoutcancel", name="checkoutcancel")
     */
    public function checkoutcancel()
    {

        return $this->render('vip/checkoutcancel.html.twig');
    }


    /**
     * @Route("/webhook", name="webhook", methods={"POST"})
     */
    public function Webhook(LoggerInterface $loggerInterface, UserRepository $userRepository, EntityManagerInterface $em, VipValidation $vipValidation)
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

                $loggerInterface->warning("Triggered: Payment.intent_succeded");

                // Remplacer cette fonction par le système de mise en place du status VIP en fonction du paiement effectué (sûrement un service)
                $paidAmount = $paymentIntent->amount;
                $loggerInterface->critical($paidAmount);

                // $customerMail = $paymentIntent->charges->data[0]->billing_details->email; POUR LE PREBUILD CHECKOUT
                $customerMail = $paymentIntent->receipt_email;
                $loggerInterface->critical($customerMail);

                $VipTime = $vipValidation->VipTimeCalculator($paidAmount);

                $user = $userRepository->findOneBy(['mail' => $customerMail]);

                $userTime = $user->getVip();

                $now = new DateTime('now');
                if ($userTime < $now) {
                    $AddedVipTime = $now->add($VipTime);
                    $user->setVip($AddedVipTime);
                } else {
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
