<?php

namespace Applicants\Exporter;

/**
 * PhpSerialize class.
 *
 * @package Applicants\Exporter
 */
class PhpSerialize implements Exporter
{

    /**
     * {@inheritdoc}
     */
    public function export(array $output): string
    {
        return serialize($output);
    }

}
