<?php

namespace App\Controller;

use DateTime;
use App\Service\OfferChoice;
use Psr\Log\LoggerInterface;
use App\Service\VipValidation;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VipController extends AbstractController
{
    /**
     * @Route("/vip", name="vip")
     * @IsGranted("ROLE_USER")
     */
    public function vip()
    {

        return $this->render('vip/presentation.html.twig');
    }

    /**
     * @Route("vip/offer/checkout", priority=10, name="checkout", methods={"POST"})
     * 
     */
    public function checkout(Request $request)
    {   
        // Recup du fetch
        $data = json_decode($request->getContent());

        // Connexion a notre compte stripe
        \Stripe\Stripe::setApiKey($this->getParameter('StripeSecretKey'));

        // Recup du stripe customer dans notre db
        $customerId = $this->getUser()->getStripe();

        // Creation du checkout
        $session = \Stripe\Checkout\Session::create([
            'customer' => $customerId,
            'payment_method_types' => ['card'],
            'mode' => $data->offerMode,
            'line_items' => [[
                'price' => $data->offerId,
                'quantity' => 1,
            ]],
            'success_url' => $this->generateUrl('checkoutSuccess', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('checkoutCancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return new JsonResponse(['id' => $session->id], 200);
    }

    /**
     * @Route("/vip/offer/{id}", name="offer")
     * @IsGranted("ROLE_USER")
     */
    public function offer($id, OfferChoice $offerChoice)
    {
        $Offer = $offerChoice->getOffer($id);

        $publickey = $this->getParameter('StripePublicKey');

        return $this->render('vip/offer.html.twig', [
            'stripePublicKey' => $publickey,
            'offer' => $Offer
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
                // Ecoute des events qui minteresse
            case 'checkout.session.completed':
                $logger->critical('checkout.session.completed');

                if ($event->data->object->mode == "payment") {
                    $logger->critical("et cest un SIMPLE PAIEMENT MESDAMES ET MESSIEURS");
                    // Ajout de temps vip en fonction du prix payé
                    $session = $event->data->object; // recup de l'objet \Stripe\Checkout\Session
                    // Recup montant payé
                    $paidAmount = $session->amount_total;
                    // Service pour calculer le temps vip fonction du montant payé
                    $VipTime = $vipValidation->VipTimeCalculator($paidAmount);
                    // Recup customer dont on reçoit l'event pour le retrouver dans la db
                    $user = $userRepository->findOneBy(['stripe' => $session->customer]);

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
                }

                break;
            case 'invoice.paid':
                $logger->critical('invoice.paid');

                $logger->critical("et cest un ABONNEMENT MESDAMES ET MESSIEURS");

                $invoice = $event->data->object; // recup de l'objet \Stripe\Invoice
                // Recup montant payé
                $paidAmount = $invoice->amount_paid;
                // Rajout du statut vip pour un nouveau mois
                $VipTime = $vipValidation->VipTimeCalculator($paidAmount);
                // Recup customer dont on reçoit l'event pour le retrouver dans la db
                $user = $userRepository->findOneBy(['stripe' => $invoice->customer]);

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
            case 'invoice.payment_failed':
                $logger->critical('invoice.payment_failed');
                // The payment failed or the customer does not have a valid payment method.
                // The subscription becomes past_due. Notify your customer and send them to the
                // customer portal to update their payment information.
                // => no more monthly added time
                break;
                // ... handle other event types
            case 'customer.subscription.deleted':
                $logger->critical('customer.subscription.deleted');
                // => no more monthly added time
                break;
                // ... handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        return new Response(200);
    }

    /**
     * @Route("/checkoutsuccess", name="checkoutSuccess")
     */
    public function checkoutSuccess()
    {

        return $this->render('vip/checkoutSuccess.html.twig');
    }

    /**
     * @Route("/checkoutcancel", name="checkoutCancel")
     */
    public function checkoutCancel()
    {

        return $this->render('vip/checkoutCancel.html.twig');
    }
}
