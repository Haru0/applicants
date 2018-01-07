<?php

namespace Applicants\Resolver;

/**
 * Discount class.
 *
 * @package Applicants\Resolver
 */
class Discount
{

    /**
     * Resolve discount factor depending on contract length in years.
     *
     * @param int $years
     * @return float
     */
    public static function resolve(int $years): float
    {
        $discountFactor = 1;

        switch (true) {
            case ($years > 3):
                $discountFactor -= .25;
                break;
            case ($years > 1):
                $discountFactor -= .2;
                break;
            case ($years > 0):
                $discountFactor -= .1;
                break;
            default:
                /* Do nothing. */
                break;
        }

        return $discountFactor;
    }

}
