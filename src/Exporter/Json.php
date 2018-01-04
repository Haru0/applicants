<?php

namespace Applicants\Exporter;

/**
 * Json class.
 *
 * @package Applicants\Exporter
 */
class Json implements Exporter
{

    /**
     * @var int
     */
    protected $options;

    /**
     * @var int
     */
    protected $depth;


    /**
     * Json constructor.
     *
     * @param int $options
     * @param int|null $depth
     */
    public function __construct(int $options = 0, int $depth = null)
    {
        $this->setOptions($options)->setDepth($depth);
    }


    /**
     * {@inheritdoc}
     */
    public function export(array $output): string
    {
        return @json_encode($output, $this->getOptions(), $this->getDepth());
    }


    /**
     * @return int
     */
    protected function getOptions(): int
    {
        return $this->options;
    }

    /**
     * @param int $options
     * @return Json
     */
    protected function setOptions(int $options): Json
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return int
     */
    protected function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * @param int $depth
     * @return Json
     */
    protected function setDepth(int $depth): Json
    {
        $this->depth = $depth;
        return $this;
    }

}
