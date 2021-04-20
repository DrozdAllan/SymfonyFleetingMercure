<?php

namespace App\Controller;


use App\Repository\UserRepository;
use App\Service\StripeService;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @Route("/vip-payment/{offer}", name="payment")
     * @IsGranted("ROLE_USER")
     */
    public function payment($offer, StripeService $stripeService)
    {
       
        $paymentIntent = $stripeService->getPaymentIntent($offer);

        if ($offer == 1) {
            $offre = "3 Jours";
            $amount = 18;
        }
        elseif ($offer == 2) {
            $offre = "1 Semaine";
            $amount = 42;
        }


        return $this->render('vip/payment.html.twig', [
            'clientSecret' => $paymentIntent->client_secret,
            'stripePublicKey' => $stripeService->getPublicKey(),
            'offre' => $offre,
            'amount' => $amount
        ]);
    }

}
