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
    public $providerPricing;


    /**
     * @param Context $context
     */
    public function register(Context $context)
    {
        $this->providerPricing = array_column($context->getProviders(), 'price_per_kwh', 'id');
    }

}
