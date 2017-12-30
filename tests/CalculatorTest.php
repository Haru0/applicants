<?php

namespace Tests;

use Applicants\Calculator;
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

        $providers = array(0 => array('id' => 1, 'price_per_kwh' => 0.15,), 1 => array('id' => 2, 'price_per_kwh' => 0.145,), 2 => array('id' => 3, 'price_per_kwh' => 0.145,),);
        $users = array(0 => array('id' => 1, 'provider_id' => 1, 'yearly_consumption' => 4000,), 1 => array('id' => 2, 'provider_id' => 1, 'yearly_consumption' => 2000,), 2 => array('id' => 3, 'provider_id' => 2, 'yearly_consumption' => 5000,),);
        $output = array('bills' => array(0 => array('id' => 1, 'price' => 600.0, 'user_id' => 1,), 1 => array('id' => 2, 'price' => 300.0, 'user_id' => 2,), 2 => array('id' => 3, 'price' => 725.0, 'user_id' => 3,),),);

        $this->assertEquals(
            $output,
            $calculator->calculate($users, $providers)
        );
    }

}
