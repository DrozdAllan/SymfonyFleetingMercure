<?php 


namespace App\Service;

use App\Repository\UserRepository;
use DateInterval;

class VipValidation
{

    public function VipTimeCalculator($paidAmount) {

        //TEST DU CLI STRIPE = 2000 USD A ENLEVER AVANT PROD
        if ($paidAmount == 2000) {
            $VipTime = DateInterval::createFromDateString('2 months');
        }

        if ($paidAmount == 1800) {
            $VipTime = DateInterval::createFromDateString('3 days');
        }

        elseif ($paidAmount == 4200) {
            $VipTime = DateInterval::createFromDateString('1 week');
        }

        elseif ($paidAmount == 9000) {
            $VipTime = DateInterval::createFromDateString('2 weeks');
        }

        elseif ($paidAmount == 18000) {
            $VipTime = DateInterval::createFromDateString('1 month');
        }

        elseif ($paidAmount == 36000) {
            $VipTime = DateInterval::createFromDateString('2 months');
        }

        return $VipTime;
    }

}


