<?php

namespace Applicants\Importer;

/**
 * Importer interface.
 *
 * @package Applicants\Importer
 */
interface Importer
{

    /**
     * @param string $path
     * @return array
     */
    public function import(string $path): array;

}
