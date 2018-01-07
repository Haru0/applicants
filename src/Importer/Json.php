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
     * @var int
     */
    protected $depth;

    /**
     * @var int
     */
    protected $options;


    /**
     * Json constructor.
     *
     * @param int $depth
     * @param int $options
     */
    public function __construct(int $depth = 512, int $options = 0)
    {
        $this->setDepth($depth)->setOptions($options);
    }


    /**
     * {@inheritdoc}
     */
    public function import(string $path): array
    {
        if (false == file_exists($path)) {
            throw new \Exception(sprintf('File "%s" does not exist', $path));
        }

        return
            @json_decode(file_get_contents($path), true, $this->getDepth(), $this->getOptions())
                ? : array();
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
    protected function setDepth(int $depth = 512): Json
    {
        $this->depth = $depth;
        return $this;
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

}
