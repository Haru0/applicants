<?php

namespace Applicants\Exporter;

use Applicants\Kitty;
use Cowsayphp\Farm;

/**
 * Cowsay class.
 *
 * @package Applicants\Exporter
 */
class KittySay implements Exporter
{

    /**
     * {@inheritdoc}
     */
    public function export(array $output): string
    {
        return Farm::create(Kitty::class)->say('Meow!');
    }

}
