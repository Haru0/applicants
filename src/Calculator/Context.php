<?php

namespace Applicants\Calculator;

/**
 * Context class.
 *
 * @package Applicants\Calculator
 */
class Context
{

    /**
     * @var array
     */
    public $users;

    /**
     * @var array
     */
    public $providers;

    /**
     * Context constructor.
     *
     * @param array $users
     * @param array $providers
     */
    public function __construct(array $users, array $providers)
    {
        $this->users = $users;
        $this->providers = $providers;
    }

}
