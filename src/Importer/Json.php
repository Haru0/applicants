<?php

namespace Applicants\Importer;

/**
 * Json class.
 *
 * @package Applicants\Importer
 */
class Json implements Importer
{

    /**
     * {@inheritdoc}
     */
    public function import(string $path): array
    {
        if (false == file_exists($path)) {
            throw new \Exception(sprintf('File "%s" does not exist', $path));
        }

        return @json_decode(file_get_contents($path), true) ? : array();
    }

}
