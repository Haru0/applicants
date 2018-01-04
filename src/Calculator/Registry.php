<?php

namespace Applicants\Calculator;

/**
 * Registry class.
 *
 * @package Applicants\Calculator
 */
class Registry
{

    /**
     * @var array
     */
    public $providerPricings;

    /**
     * @var array
     */
    public $contractLengths;

    /**
     * @var array
     */
    public $contractUsers;

    /**
     * @var array
     */
    public $usersConsumption;

    /**
     * @var array
     */
    public $contractProviders;

    /**
     * @var array
     */
    public $greenContracts;

    /**
     * @var array
     */
    public $cancelableProviders;

    /**
     * @var array
     */
    public $canceledProviderContracts = array();

    public $contractDates;


    /**
     * @param Context $context
     */
    public function register(Context $context)
    {
        $this->registerContractDates($context->getContracts());
        $this->registerProviderPricings($context->getProviders());
        $this->registerContractLengths($context->getContracts(), $context->getContractModifications());
        $this->registerContractUsers($context->getContracts());
        $this->registerUsersConsumption($context->getUsers());
        $this->registerContractProviders($context->getContracts(), $context->getContractModifications());
        $this->registerGreenContractors($context->getContracts());
        $this->registerCancelableProviders($context->getProviders());
        $this->registerContractModifications($context->getContractModifications());
    }

    /**
     * @param array $contractModifications
     * @return $this
     */
    protected function registerContractModifications(array $contractModifications)
    {
        foreach ($contractModifications as $modification) {
            $contract = $modification['contract_id'];
            list($start, $end) = array_values($this->contractDates[$contract]);

            if (isset($modification['start_date'])) {
                $start = \DateTime::createFromFormat('Y-m-d', $modification['start_date']);
            } else {
                $start = \DateTime::createFromFormat('Y-m-d', $start);
            }

            if (isset($modification['end_date'])) {
                $end = \DateTime::createFromFormat('Y-m-d', $modification['end_date']);
            } else {
                $end = \DateTime::createFromFormat('Y-m-d', $end);
            }

            $length = $this->calculateLength($start, $end);

            if (isset($modification['provider_id'])) {
                $this->contractLengths[] = $length;
                $this->contractProviders[] = $modification['provider_id'];
                $this->contractUsers[] = $this->contractUsers[$contract];
                $this->canceledProviderContracts[] = $contract;
            } else {
                $this->contractLengths[$contract] = $length;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getContracts()
    {
        return array_keys($this->contractLengths);
    }

    /**
     * @param array $contracts
     * @return $this
     */
    protected function registerContractDates(array $contracts)
    {
        $this->contractDates = array_map(
            function (array $contract) {
                return array_only($contract, array('start_date', 'end_date'));
            },
            array_column($contracts, null, 'id')
        );

        return $this;
    }

    /**
     * @param array $providers
     * @return $this
     */
    protected function registerProviderPricings(array $providers)
    {
        $this->providerPricings = array_column($providers, 'price_per_kwh', 'id');
        return $this;
    }

    /**
     * @param array $contracts
     * @param array $contractModifications
     * @return $this
     */
    protected function registerContractLengths(array $contracts, array $contractModifications)
    {
        $this->contractLengths = array_map(
            array($this, 'mapLength'),
            array_column($contracts, null, 'id')
        );

        return $this;
    }

    /**
     * @param array $contracts
     * @return $this
     */
    protected function registerContractUsers(array $contracts)
    {
        $this->contractUsers = array_column($contracts, 'user_id', 'id');
        return $this;
    }

    /**
     * @param array $users
     * @return $this
     */
    protected function registerUsersConsumption(array $users)
    {
        $this->usersConsumption = array_column($users, 'yearly_consumption', 'id');
        return $this;
    }

    /**
     * @param array $contracts
     * @param array $contractModifications
     * @return $this
     */
    protected function registerContractProviders(array $contracts, array $contractModifications)
    {
        $this->contractProviders = array_column($contracts, 'provider_id', 'id');
        return $this;
    }

    /**
     * @param array $contracts
     * @return $this
     */
    protected function registerGreenContractors(array $contracts)
    {
        $this->greenContracts = array_filter(
            array_column($contracts, 'green', 'id'),
            array($this, 'isTrue')
        );

        return $this;
    }

    /**
     * @param array $providers
     * @return $this
     */
    protected function registerCancelableProviders(array $providers)
    {
        $this->cancelableProviders = array_filter(
            array_column($providers, 'cancellation_fee', 'id'),
            array($this, 'isTrue')
        );

        return $this;
    }


    /**
     * @param array $contract
     * @return string
     */
    protected function mapLength(array $contract)
    {
        $start = \DateTime::createFromFormat('Y-m-d', $contract['start_date']);
        $end = \DateTime::createFromFormat('Y-m-d', $contract['end_date']);

        return $this->calculateLength($start, $end);
    }

    protected function calculateLength(\DateTime $start, \DateTime $end)
    {
        return (float)number_format(($end->diff($start)->format('%a') / 365), 2);
    }


    /**
     * @param $value
     * @return bool
     */
    protected function isTrue($value): bool
    {
        return (true === $value);
    }

}
