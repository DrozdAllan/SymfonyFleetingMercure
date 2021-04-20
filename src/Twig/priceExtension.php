<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class priceExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'price'])
        ];
    }

    public function price($value) {
        //1800
        $finalValue = $value / 100;
        //18,00
        $finalValue = number_format($finalValue, 2, ',', ' ');
        
        return $finalValue . '€';
    }
}
