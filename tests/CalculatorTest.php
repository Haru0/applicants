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

        $contractModifications = array(0 => array('contract_id' => 1, 'end_date' => '2018-12-25', 'id' => 1,), 1 => array('contract_id' => 1, 'end_date' => '2019-12-25', 'id' => 2, 'provider_id' => 3, 'start_date' => '2018-12-25',),);
        $contracts = array(0 => array('end_date' => '2019-12-25', 'green' => false, 'id' => 1, 'provider_id' => 1, 'start_date' => '2017-12-25', 'user_id' => 1,), 1 => array('end_date' => '2018-12-25', 'green' => true, 'id' => 2, 'provider_id' => 1, 'start_date' => '2017-12-25', 'user_id' => 2,), 2 => array('end_date' => '2018-12-25', 'green' => false, 'id' => 3, 'provider_id' => 2, 'start_date' => '2017-12-25', 'user_id' => 3,),);
        $providers = array(0 => array('cancellation_fee' => true, 'id' => 1, 'price_per_kwh' => 0.15,), 1 => array('cancellation_fee' => true, 'id' => 2, 'price_per_kwh' => 0.145,), 2 => array('cancellation_fee' => false, 'id' => 3, 'price_per_kwh' => 0.145,),);
        $users = array(0 => array('id' => 1, 'yearly_consumption' => 4000,), 1 => array('id' => 2, 'yearly_consumption' => 2000,), 2 => array('id' => 3, 'yearly_consumption' => 5000,),);
        $output = array('bills' => array(0 => array('commission' => array('insurance_fee' => 18.25, 'provider_fee' => 571.75, 'selectra_fee' => 71.47,), 'id' => 1, 'price' => 540, 'user_id' => 1,), 1 => array('commission' => array('insurance_fee' => 18.25, 'provider_fee' => 503.75, 'selectra_fee' => 62.97,), 'id' => 2, 'price' => 522, 'user_id' => 1,), 2 => array('commission' => array('insurance_fee' => 18.25, 'provider_fee' => 151.75, 'selectra_fee' => 18.97,), 'id' => 3, 'price' => 170, 'user_id' => 2,), 3 => array('commission' => array('insurance_fee' => 18.25, 'provider_fee' => 634.25, 'selectra_fee' => 79.28,), 'id' => 4, 'price' => 652.5, 'user_id' => 3,),));

        $context = new Context($users, $providers);
        $context
            ->setContracts($contracts)
            ->setContractModifications($contractModifications);

        $this->assertEquals(
            $output,
            $calculator->calculate($context)
        );
    }

}
