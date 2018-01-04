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

        foreach ($this->registry->getContracts() as $contract) {
            $bills[] = array_merge(
                array('id' => $index++),
                $this->calculateBill($contract)
            );
        }

        return array(
            'bills' => $bills,
        );
    }

    /**
     * @param array $contract
     * @return array
     */
    protected function calculateBill($contract): array
    {

        return array(
            'price' => ($price = $this->calculatePrice(($user = $this->registry->contractUsers[$contract]), $contract)),
            'commission' => $this->calculateCommission($contract, $price),
            'user_id' => $user,
        );
    }

    /**
     * @param $user
     * @param $contract
     * @return float
     */
    protected function calculatePrice($user, $contract): float
    {
        $consumption = $this->registry->usersConsumption[$user];

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

        $lengths = $lengths1 = $this->registry->contractSeasons[$contract];
        $pricing = $this->registry->providerPricings[$provider];

        array_walk(
            $lengths,
            function (& $days, $season, array $userdata = null) use ($consumption, $pricing, $contract) {
                $seasonConsumption = $consumption * $days / (365 * $this->registry->contractLengths[$contract]);
                $seasonPricing = ($pricing * (1 + $userdata[$season]));

                $days = $seasonPricing * $seasonConsumption;

                return;
            },
            array(
                'spring' => .01,
                'summer' => -.015,
                'fall' => .007,
                'winter' => .0,
            )
        );

        $price = $discountFactor * array_sum($lengths);

        if (true == array_key_exists($contract, $this->registry->greenContracts)) {
            $price -= ($consumption * .05);
        }

        return $price;
    }

    /**
     * @param $contract
     * @param $price
     * @return array
     */
    protected function calculateCommission($contract, $price)
    {
        $length = $this->registry->contractLengths[$contract];
        $provider = $this->registry->contractProviders[$contract];

        $insuranceFee = (365 * .05 * $length);
        $providerFee = ($price - $insuranceFee);

        if (
            (true == @$this->registry->cancelableProviders[$provider]) &&
            (true == in_array($contract, $this->registry->canceledProviderContracts))
        ) {
            $providerFee += 50;
        }

        $selectraFee = ($providerFee * .125);

        return array(
            'insurance_fee' => round($insuranceFee, 2),
            'provider_fee' => round($providerFee, 2),
            'selectra_fee' => round($selectraFee, 2),
        );
    }

}
