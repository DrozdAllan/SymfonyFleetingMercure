<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Service\VipValidation;
use App\Repository\UserRepository;
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
        
        $customerMail = "bubu@gmail.com";

        $paidAmount = 2000;

        $VipTime = $vipValidation->VipTimeCalculator($paidAmount);

        $User = $userRepository->findOneBy(['mail' => $customerMail]);

        $Now = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $VipExpireDate = $Now->add($VipTime); 

        // dd($VipExpireDate);
        /** @var User $User */
        
        $User->setVip($VipExpireDate);
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
     * @Route("/vip/offer", name="offerverify")
     */
    public function offerverify()
    {

        $publickey = $this->getParameter('StripePublicKey');

        return $this->render('vip/offerverify.html.twig', [
            'publickey' => $publickey
        ]);
    }

    /**
     * @Route("/vip/checkout", name="checkout")
     * @IsGranted("ROLE_USER")
     */
    public function checkout()
    {
        \Stripe\Stripe::setApiKey($this->getParameter('StripeSecretKey'));

        $checkout_session = \Stripe\Checkout\Session::create([
            'customer_email' => 'test@gmail.com',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => 2000,
                    'product_data' => [
                        'name' => 'Abonnement Fleeting VIP',
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('checkoutsuccess', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('checkoutcancel', [], UrlGeneratorInterface::ABSOLUTE_URL),

        ]);

        return new JsonResponse(['id' => $checkout_session->id]);
    }

    /**
     * @Route("/vip/checkout/success", name="checkoutsuccess")
     */
    public function checkoutsuccess()
    {

        return $this->render('vip/checkoutsuccess.html.twig');
    }


    /**
     * @Route("/vip/checkout/cancel", name="checkoutcancel")
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

                $loggerInterface->warning("payment_intent.succeeded");

                // Remplacer cette fonction par le système de mise en place du status VIP en fonction du paiement effectué (sûrement un service)
                $paidAmount = $paymentIntent->amount;
                $loggerInterface->critical($paidAmount);
                // 2000
                
                $customerMail = $paymentIntent->charges->data[0]->billing_details->email;
                $loggerInterface->critical($customerMail);

                $VipTime = $vipValidation->VipTimeCalculator($paidAmount);
                $loggerInterface->critical($VipTime);
            
                break;
                // ... handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
        }


        return new Response(200);
    }
}
