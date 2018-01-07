<?php

namespace Applicants\Calculator;

use Applicants\Resolver\Season as SeasonResolver;

/**
 * Registry class.
 *
 * @package Applicants\Calculator
 */
class Registry implements RegistryInterface
{

    /**
     * @var array
     */
    public $providerPricings = array();

    /**
     * @var array
     */
    public $contractLengths = array();

    /**
     * @var array
     */
    public $contractDates = array();

    /**
     * @var array
     */
    public $contractSeasons = array();

    /**
     * @var array
     */
    public $contractUsers = array();

    /**
     * @var array
     */
    public $usersConsumption = array();

    /**
     * @var array
     */
    public $contractProviders = array();

    /**
     * @var array
     */
    public $greenContracts = array();

    /**
     * @var array
     */
    public $cancelableProviders = array();

    /**
     * @var array
     */
    public $canceledProviderContracts = array();

    /**
     * @param Context $context
     */
    public function register(Context $context): void
    {
        $this->registerContractDates($context->getContracts());
        $this->registerProviderPricings($context->getProviders());
        $this->registerContractLengthsAndDates($context->getContracts());
        $this->registerContractUsers($context->getContracts());
        $this->registerUsersConsumption($context->getUsers());
        $this->registerContractProviders($context->getContracts());
        $this->registerGreenContractors($context->getContracts());
        $this->registerCancelableProviders($context->getProviders());
        $this->registerContractModifications($context->getContractModifications());
    }

    /**
     * Clear stored values.
     */
    public function clean(): void
    {
        $this->providerPricings =
        $this->contractLengths =
        $this->contractUsers =
        $this->usersConsumption =
        $this->contractProviders =
        $this->greenContracts =
        $this->cancelableProviders =
        $this->canceledProviderContracts =
        $this->contractDates =
        $this->contractSeasons = array();
    }


    /**
     * @param int $contract
     * @return int
     */
    public function getContractUser(int $contract): int
    {
        return $this->contractUsers[$contract];
    }

    /**
     * @param int $contract
     * @return float
     */
    public function getContractLength(int $contract): float
    {
        return $this->contractLengths[$contract];
    }

    public function getUserConsumption(int $user): int
    {
        return $this->usersConsumption[$user];
    }

    /**
     * @param int $contract
     * @return int
     */
    public function getContractProvider(int $contract): int
    {
        return $this->contractProviders[$contract];
    }

    /**
     * @param int $contract
     * @return bool
     */
    public function isContractCanceled(int $contract): bool
    {
        $provider = $this->contractProviders[$contract];

        return (
            (true == @$this->cancelableProviders[$provider]) &&
            (true == in_array($contract, $this->canceledProviderContracts))
        );
    }


    /**
     * @param array $contractModifications
     * @return Registry
     */
    protected function registerContractModifications(array $contractModifications): Registry
    {
        /** @var array $modification */
        foreach ($contractModifications as $modification) {
            if (false == array_key_exists('contract_id', $modification)) {
                continue;
            }

            $contract = $modification['contract_id'];
            list($end, $start) = array_values($this->contractDates[$contract]);

            if (isset($modification['start_date'])) {
                $start = $this->mapDate($modification['start_date']);
            } else {
                $start = $this->mapDate($start);
            }

            if (isset($modification['end_date'])) {
                $end = $this->mapDate($modification['end_date']);
            } else {
                $end = $this->mapDate($end);
            }

            $length = $this->calculateLength($start, $end);

            if (isset($modification['provider_id'])) {
                $this->contractLengths[] = $length;
                $this->contractProviders[] = $modification['provider_id'];
                $this->contractUsers[] = $this->contractUsers[$contract];
                $this->canceledProviderContracts[] = $contract;
                $this->contractSeasons[] = $this->calculateSeasonLengths($start, $end);
            } else {
                $this->contractLengths[$contract] = $length;
                $this->contractSeasons[$contract] = $this->calculateSeasonLengths($start, $end);
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
     * @return Registry
     */
    protected function registerContractDates(array $contracts): Registry
    {
        $this->contractDates = array_map(
            function (array $contract) {
                return array_only($contract, array('start_date', 'end_date'));
            },
            array_column($contracts, null, 'id')
        );

        $this->contractSeasons = array_map(
            function (array $dates) {
                list($end, $start) = array_values($dates);

                return $this->calculateSeasonLengths(
                    $this->mapDate($start),
                    $this->mapDate($end)
                );
            },
            $this->contractDates
        );

        return $this;
    }

    /**
     * @param array $providers
     * @return Registry
     */
    protected function registerProviderPricings(array $providers): Registry
    {
        $this->providerPricings = array_column($providers, 'price_per_kwh', 'id');
        return $this;
    }

    /**
     * @param array $contracts
     * @return $this
     */
    protected function registerContractLengthsAndDates(array $contracts)
    {
        $this->contractLengths = array_map(
            array($this, 'mapContractLength'),
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
     * @return float
     */
    protected function mapContractLength(array $contract): float
    {
        return $this->calculateLength(
            $this->mapDate($contract['start_date']),
            $this->mapDate($contract['end_date'])
        );
    }

    /**
     * @param string $date
     * @param string $format
     * @return \DateTime
     * @throws \Exception
     */
    protected function mapDate(string $date, string $format = 'Y-m-d'): \DateTime
    {
        if (false !== ($date = \DateTime::createFromFormat($format, $date))) {
            return $date;
        }

        throw new \Exception('Invalid date');
    }

    /**
     * @todo Make it more precise (include leap-years).
     *
     * @param \DateTime $start
     * @param \DateTime $end
     * @return float
     */
    protected function calculateLength(\DateTime $start, \DateTime $end): float
    {
        return round(($end->diff($start)->format('%a') / 365), 2);
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @return array
     */
    protected function calculateSeasonLengths(\DateTime $start, \DateTime $end): array
    {
        $seasons = array(
            SeasonResolver::SPRING => 0,
            SeasonResolver::SUMMER => 0,
            SeasonResolver::FALL => 0,
            SeasonResolver::WINTER => 0,
        );

        $iterator = clone($start);

        while ($iterator < $end) {
            $seasons[SeasonResolver::resolve($start)] += 1;
            $iterator->add(new \DateInterval('P1D'));
        }

        return $seasons;
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
