<?php

namespace Applicants;

use Applicants\Calculator\Context;

/**
 * Calculator class.
 *
 * @package Applicants
 */
class Calculator
{

    /**
     * @param Context $context
     * @return array
     */
    public function calculate(Context $context): array
    {
        $index = 1;
        $bills = array();

        $providersTransformed = array_column($context->providers, 'price_per_kwh', 'id');

        foreach ($context->users as $user) {
            $bills[] = array(
                'id' => $index++,
                'price' => $providersTransformed[$user['provider_id']] * $user['yearly_consumption'],
                'user_id' => $user['id'],
            );
        }

        return array(
            'bills' => $bills,
        );
    }

}

