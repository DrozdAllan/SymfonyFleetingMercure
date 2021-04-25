<?php


namespace App\Service;


class OfferDecision
{

    public function OfferCalculator($offerId)
    {

        if ($offerId == 1) {
            $VipOffer = "3 days";
            $VipPrice = 1800;
        }
        if ($offerId == 2) {
            $VipOffer = "1 week";
            $VipPrice = 4200;
        } elseif ($offerId == 3) {
            $VipOffer = "2 weeks";
            $VipPrice = 9000;
        } elseif ($offerId == 4) {
            $VipOffer = "1 month";
            $VipPrice = 18000;
        } elseif ($offerId == 5) {
            $VipOffer = "2 months";
            $VipPrice = 36000;
        }

        return (object) ['VipOffer' => $VipOffer, 'VipPrice' => $VipPrice];
    }
}
