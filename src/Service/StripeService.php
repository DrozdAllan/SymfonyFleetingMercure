<?php

namespace App\Service;


class StripeService
{

    protected $secretKey;
    protected $publicKey;

    public function __construct(string $secretKey, string $publicKey)
    {
        $this->secretKey = $secretKey;
        $this->publicKey = $publicKey;
    }
    
    public function getPublicKey() {
        return $this->publicKey;
    }
    
    public function getPaymentIntent($offer)
    {

        // This is your real test secret API key.
        \Stripe\Stripe::setApiKey($this->secretKey);


        function calculateOrderAmount($offer): int
        {
            // Replace this constant with a calculation of the order's amount
            // Calculate the order total on the server to prevent
            // customers from directly manipulating the amount on the client
            if ($offer == 1) {
                return 1800;
            }
            elseif ($offer == 2) {
                return 4200;
            }
        }

        return \Stripe\PaymentIntent::create([
            'amount' => calculateOrderAmount($offer),
            'currency' => 'eur',
        ]);
    }
}
