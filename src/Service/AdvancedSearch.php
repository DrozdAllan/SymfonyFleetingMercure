<?php


namespace App\Service;

class AdvancedSearch
{

    public function find($elements)
    {
        $criteria = [];
        foreach ($elements as $key => $value) {
            if ($value !== null) {
                $criteria[$key] = $value;
            }
        }

        return $criteria;
    }
}
