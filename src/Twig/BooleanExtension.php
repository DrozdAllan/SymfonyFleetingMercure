<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class BooleanExtension extends AbstractExtension {
    public function getFilters()
    {
        return [
            new TwigFilter('boolean', [$this, 'boolean'])
        ];
    }

    public function boolean($value) {
        // 1
        if ($value == 1) {
        $finalValue = 'Oui';
        }
        else {
        $finalValue = 'Non';
        }
        return $finalValue;
    }











}