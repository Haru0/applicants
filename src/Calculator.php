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
            'price' => ($price = $this->calculatePrice($user['id'], $user['yearly_consumption'])),
            'commission' => $this->calculateCommission($user['id'], $price),
            'user_id' => $user['id'],
        );
    }

    /**
     * @param $user
     * @param $consumption
     * @return float
     */
    protected function calculatePrice($user, $consumption): float
    {
        $contract = array_search($user, $this->registry->contractUsers);

        $length = $this->registry->contractLengths[$contract];
        $provider = $this->registry->contractProviders[$contract];

        $discountFactor = 1;

        switch (true) {
            case ($length > 3):
                $discountFactor -= .25;
                break;
            case ($length > 1):
                $discountFactor -= .2;
                break;
            case ($length > 0):
                $discountFactor -= .1;
                break;
            default:
                /* Do nothing. */
                break;
        }

        $price =
            $discountFactor *
            (
                $length *
                $consumption *
                $this->registry->providerPricings[$provider]
            );

        if (true == array_key_exists($contract, $this->registry->greenContracts)) {
            $price -= ($consumption * .05);
        }

        return $price;
    }

    /**
     * @param $user
     * @param $price
     * @return array
     */
    protected function calculateCommission($user, $price)
    {
        $contract = array_search($user, $this->registry->contractUsers);
        $length = $this->registry->contractLengths[$contract];

        $insuranceFee = (365 * .05 * $length);
        $providerFee = ($price - $insuranceFee);
        $selectraFee = ($providerFee * .125);

        return array(
            'insurance_fee' => number_format($insuranceFee, 2),
            'provider_fee' => number_format($providerFee, 2),
            'selectra_fee' => number_format($selectraFee, 2),
        );
    }

}
