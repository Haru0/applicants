<?php

namespace Applicants;

use Applicants\Calculator\Context;
use Applicants\Calculator\Registry;

/**
 * Calculator class.
 *
 * @package Applicants
 */
class Calculator
{

    /**
     * @var Registry
     */
    protected $registry;


    /**
     * Calculator constructor.
     */
    public function __construct()
    {
        $this->registry = new Registry();
    }


    /**
     * @param Context $context
     * @return array
     */
    public function calculate(Context $context): array
    {
        $this->registry->register($context);

        $index = 1;
        $bills = array();

        foreach ($context->getUsers() as $user) {
            $bills[] = array_merge(
                array('id' => $index++),
                $this->calculateBill($user)
            );
        }

        return array(
            'bills' => $bills,
        );
    }

    /**
     * @param array $user
     * @return array
     */
    protected function calculateBill(array $user): array
    {
        return array(
            'price' => $this->calculatePrice($user['yearly_consumption'], $user['provider_id']),
            'user_id' => $user['id'],
        );
    }

    /**
     * @param $consumption
     * @param $provider
     * @return float
     */
    protected function calculatePrice($consumption, $provider): float
    {
        return $this->registry->providerPricing[$provider] * $consumption;
    }

}
