<?php

namespace Tests;

use Applicants\Calculator;
use Applicants\Calculator\Context;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

/**
 * Calculator class.
 *
 * @package Tests
 */
class CalculatorTest extends TestCase
{

    /**
     * @var vfsStreamDirectory
     */
    protected $root;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->root = vfsStream::setup('root', null, array());
    }


    /**
     * Test calculation.
     */
    public function testCalculatePart1Level1()
    {
        $calculator = new Calculator();

        $contracts = array(0 => array('contract_length' => 2, 'id' => 1, 'provider_id' => 1, 'user_id' => 1,), 1 => array('contract_length' => 1, 'id' => 2, 'provider_id' => 1, 'user_id' => 2,), 2 => array('contract_length' => 1, 'id' => 3, 'provider_id' => 2, 'user_id' => 3,),);
        $providers = array(0 => array('id' => 1, 'price_per_kwh' => 0.15,), 1 => array('id' => 2, 'price_per_kwh' => 0.145,), 2 => array('id' => 3, 'price_per_kwh' => 0.145,),);
        $users = array(0 => array('id' => 1, 'yearly_consumption' => 4000,), 1 => array('id' => 2, 'yearly_consumption' => 2000,), 2 => array('id' => 3, 'yearly_consumption' => 5000,),);
        $output = array('bills' => array(0 => array('id' => 1, 'price' => 960.0, 'commission' => array('insurance_fee' => '36.50', 'provider_fee' => '923.50', 'selectra_fee' => '115.44',), 'user_id' => 1,), 1 => array('id' => 2, 'price' => 270.0, 'commission' => array('insurance_fee' => '18.25', 'provider_fee' => '251.75', 'selectra_fee' => '31.47',), 'user_id' => 2,), 2 => array('id' => 3, 'price' => 652.5, 'commission' => array('insurance_fee' => '18.25', 'provider_fee' => '634.25', 'selectra_fee' => '79.28',), 'user_id' => 3,),),);

        $context = new Context($users, $providers);
        $context->setContracts($contracts);

        $this->assertEquals(
            $output,
            $calculator->calculate($context)
        );
    }

}
