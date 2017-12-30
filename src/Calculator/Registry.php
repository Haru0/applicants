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
    public $contractProviders;

    /**
     * @var array
     */
    public $greenContracts;


    /**
     * @param Context $context
     */
    public function register(Context $context)
    {
        $this->registerProviderPricings($context->getProviders());
        $this->registerContractLengths($context->getContracts());
        $this->registerContractUsers($context->getContracts());
        $this->registerContractProviders($context->getContracts());
        $this->registerGreenContractors($context->getContracts());
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
     * @return $this
     */
    protected function registerContractLengths(array $contracts)
    {
        $this->contractLengths = array_column($contracts, 'contract_length', 'id');
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
     * @param array $contracts
     * @return $this
     */
    protected function registerContractProviders(array $contracts)
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
        $this->greenContracts = array_filter(array_column($contracts, 'green', 'id'), array($this, 'isTrue'));
        return $this;
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
