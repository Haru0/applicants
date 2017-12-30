<?php

namespace Applicants;

/**
 * Calculator class.
 *
 * @package Applicants
 */
class Calculator
{

    /**
     * @param array $users
     * @param array $providers
     * @return array
     */
    public function calculate(array $users, array $providers): array
    {
        $index = 1;
        $bills = array();

        $providersTransformed = array_column($providers, 'price_per_kwh', 'id');

        foreach ($users as $value) {
            $bills[] = array(
                'id' => $index++,
                'price' => $providersTransformed[$value['provider_id']] * $value['yearly_consumption'],
                'user_id' => $value['id'],
            );
        }

        return array(
            'bills' => $bills,
        );
    }

}

