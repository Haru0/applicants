<?php

namespace Applicants\Exporter;

/**
 * Exporter interface.
 *
 * @package Applicants\Exporter
 */
interface Exporter
{

    /**
     * @param array $path
     * @return string
     */
    public function export(array $path): string;

}
