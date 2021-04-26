<?php 


namespace App\Service;

use DateInterval;

class VipValidation
{
    public function VipTimeCalculator($paidAmount) {

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


