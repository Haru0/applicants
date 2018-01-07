<?php

namespace Applicants\Exporter;

use Camspiers\JsonPretty\JsonPretty;

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
     * @var int|null
     */
    protected $depth;


    /**
     * Json constructor.
     *
     * @param int $options
     * @param int $depth
     */
    public function __construct(int $options = 0, int $depth = 512)
    {
        $this->setOptions($options)->setDepth($depth);
    }


    /**
     * {@inheritdoc}
     */
    public function export(array $output): string
    {
        return $this->prettify(@json_encode($output, $this->getOptions(), $this->getDepth()));
    }


    /**
     * @param $json
     * @return string
     */
    protected function prettify($json): string
    {
        return (new JsonPretty())->prettify($json);
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
    protected function setOptions(int $options = 0): Json
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return int|null
     */
    protected function getDepth()
    {
        return $this->depth;
    }

    /**
     * @param int $depth
     * @return Json
     */
    protected function setDepth(int $depth = 512): Json
    {
        $this->depth = $depth;
        return $this;
    }

}
