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
    protected $users;

    /**
     * @var array
     */
    protected $providers;

    /**
     * @var array
     */
    protected $contracts;

    /**
     * Context constructor.
     *
     * @param array $users
     * @param array $providers
     */
    public function __construct(array $users, array $providers)
    {
        $this->setUsers($users)->setProviders($providers);
    }


    /**
     * @return array
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @param array $users
     * @return Context
     */
    public function setUsers(array $users): Context
    {
        $this->users = $users;
        return $this;
    }

    /**
     * @return array
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * @param array $providers
     * @return Context
     */
    public function setProviders(array $providers): Context
    {
        $this->providers = $providers;
        return $this;
    }

    /**
     * @return array
     */
    public function getContracts(): array
    {
        return $this->contracts;
    }

    /**
     * @param array $contracts
     * @return Context
     */
    public function setContracts(array $contracts): Context
    {
        $this->contracts = $contracts;
        return $this;
    }

}
