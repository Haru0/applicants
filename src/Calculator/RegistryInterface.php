<?php

namespace Applicants\Calculator;

/**
 * RegistryInterface interface.
 *
 * @package Applicants\Calculator
 */
interface RegistryInterface
{

    /**
     * @param int $contract
     * @return int
     */
    public function getContractUser(int $contract): int;

    /**
     * @param int $contract
     * @return float
     */
    public function getContractLength(int $contract): float;

    /**
     * @param int $contract
     * @return int
     */
    public function getContractProvider(int $contract): int;


    /**
     * Check whether contract was canceled or not.
     *
     * @param int $contract
     * @return bool
     */
    public function isContractCanceled(int $contract): bool;

}
