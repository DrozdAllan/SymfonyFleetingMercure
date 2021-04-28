<?php


namespace App\Service;


class OfferChoice
{

    public function getOffer($id)
    {
        $offerText = '';
        $offerPrice = '';
        $offerMode = '';
        $offerId = '';

        if ($id == 1) {
            $offerText = "3 days";
            $offerPrice = 1800;
            $offerId = "price_1IkDZuDTsj5RSWQCwzU8LCtc";
            $offerMode = 'payment';
        } elseif ($id == 2) {
            $offerText = "1 week";
            $offerPrice = 4200;
            $offerId = "price_1IkDZuDTsj5RSWQCVCJjMQjT";
            $offerMode = 'payment';
        } elseif ($id == 3) {
            $offerText = "2 weeks";
            $offerPrice = 9000;
            $offerId = "price_1IkDZuDTsj5RSWQCOuyzExjS";
            $offerMode = 'payment';
        } elseif ($id == 4) {
            $offerText = "1 month";
            $offerPrice = 18000;
            $offerId = "price_1IkDZuDTsj5RSWQCwm4w9wOM";
            $offerMode = 'payment';
        } elseif ($id == 5) {
            $offerText = "2 months";
            $offerPrice = 36000;
            $offerId = "price_1IkDZuDTsj5RSWQCpzrFcsGi";
            $offerMode = 'payment';
        } elseif ($id == 6) {
            $offerText = "monthly subscribe";
            $offerPrice = 3490;
            $offerId = "price_1IkDp4DTsj5RSWQCno9DR63U";
            $offerMode = 'subscription';
        }

        return (object) ['offerText' => $offerText, 'offerPrice' => $offerPrice, 'offerId' => $offerId, 'offerMode' => $offerMode];
    }
}
