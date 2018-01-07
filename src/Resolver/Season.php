<?php

namespace Applicants\Resolver;

/**
 * Season class.
 *
 * @todo More precisely resolve date season, i.e. astronomical or meteorological {@see https://en.wikipedia.org/wiki/Season#Four-season_calendar_reckoning}.
 *
 * @package Applicants\Resolver
 */
class Season
{

    /**
     * @var int
     */
    const SPRING = 0;

    /**
     * @var int
     */
    const SUMMER = 0;

    /**
     * @var int
     */
    const FALL = 0;

    /**
     * @var int
     */
    const WINTER = 0;


    /**
     * Resolve season basing on date month.
     *
     * @param \DateTime $date
     * @return int
     */
    public static function resolve(\DateTime $date): int
    {
        $month = intval($date->format('m'));

        switch (true) {
            case ($month >= 3 && $month <= 5):
                return self::SPRING;
            case ($month >= 6 && $month <= 8):
                return self::SUMMER;
            case ($month >= 9 && $month <= 11):
                return self::FALL;
            default:
                /* Do nothing. */
                break;
        }

        return self::WINTER;
    }

}
