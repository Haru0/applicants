<?php

namespace Applicants;

use Applicants\Calculator\Context;
use Applicants\Calculator\Registry;
use Applicants\Calculator\RegistryInterface;
use Applicants\Resolver\Discount as DiscountResolver;
use Applicants\Resolver\Season as SeasonResolver;

/**
 * Calculator class.
 *
 * @method getContractUser(int $contract): int
 * @method getContractLength(int $contract): float
 * @method getUserConsumption(int $user): int
 * @method getContractProvider(int $contract): int
 * @method isContractCanceled(int $contract): bool
 *
 * @package Applicants
 */
class Calculator
{

    /**
     * @var float
     */
    const INSURANCE_FEE_FACTOR = .05;

    /**
     * @var float
     */
    const SELECTRA_FEE_FACTOR = .125;

    /**
     * Value of fee given to canceled contract.
     *
     * @var float
     */
    const CANCELLATION_CHARGE = 50.0;


    /**
     * Percentage amount of consumption factor in related seasons.
     *
     * @var array
     */
    protected static $seasonConsumptionHandicaps = array(
        SeasonResolver::SPRING => .01,
        SeasonResolver::SUMMER => -.015,
        SeasonResolver::FALL => .007,
        SeasonResolver::WINTER => .0,
    );


    /**
     * Registry holding input data and providing developer-friendly API to perform operations on.
     *
     * @var RegistryInterface
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
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws \Exception
     */
    public function __call(string $name, array $arguments = array())
    {
        if (true == method_exists($this->registry, $name)) {
            return call_user_func_array(array($this->registry, $name), $arguments);
        }

        throw new \Exception('Invalid registry method');
    }


    /**
     * @param Context $context
     * @return array
     */
    public function calculate(Context $context): array
    {
        $registry = $this->registry;
        $registry->register($context);

        $index = 1;
        $bills = array();

        /** @var int $contract */
        foreach ($this->registry->getContracts() as $contract) {
            $bills[] = array_merge(
                array('id' => $index++),
                $this->calculateBill($contract)
            );
        }

        $registry->clean();

        return array(
            'bills' => $bills,
        );
    }


    /**
     * @param int $contract
     * @return array
     */
    protected function calculateBill(int $contract): array
    {
        $price = $this->calculatePrice($contract);
        $commission = $this->calculateCommission($contract, $price);

        $user = $this->getContractUser($contract);

        return array(
            'price' => $price,
            'commission' => $commission,
            'user_id' => $user,
        );
    }

    /**
     * @param int $contract
     * @return float
     */
    protected function calculatePrice(int $contract): float
    {
        $user = $this->getContractUser($contract);
        $consumption = $this->getUserConsumption($user);

        $length = $this->getContractLength($contract);
        $provider = $this->getContractProvider($contract);

        $lengths = $this->registry->contractSeasons[$contract];
        $pricing = $this->registry->providerPricings[$provider];

        /* Calculate each season price. Lengths becomes season prices. */
        array_walk(
            $lengths,
            function (& $days, $season, array $seasonConsumptionHandicaps = null) use ($consumption, $pricing, $contract) {
                $seasonConsumption = $consumption * $days / (365 * $this->registry->contractLengths[$contract]);
                $seasonPricing = ($pricing * (1 + $seasonConsumptionHandicaps[$season]));

                $days = ($seasonPricing * $seasonConsumption);

                return;
            },
            self::$seasonConsumptionHandicaps
        );

        $price = (DiscountResolver::resolve($length) * array_sum($lengths));

        if (true == array_key_exists($contract, $this->registry->greenContracts)) {
            $price -= ($consumption * .05);
        }

        return round($price, 2);
    }

    /**
     * @param $contract
     * @param $price
     * @return array
     */
    protected function calculateCommission($contract, $price)
    {
        $length = $this->getContractLength($contract);

        $insuranceFee = (365 * $length * self::INSURANCE_FEE_FACTOR);
        $providerFee = ($price - $insuranceFee);

        if (true == $this->isContractCanceled($contract)) {
            $providerFee += self::CANCELLATION_CHARGE;
        }

        $selectraFee = ($providerFee * self::SELECTRA_FEE_FACTOR);

        return array(
            'insurance_fee' => round($insuranceFee, 2),
            'provider_fee' => round($providerFee, 2),
            'selectra_fee' => round($selectraFee, 2),
        );
    }

}
