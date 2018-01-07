<?php

namespace Applicants\Command;

use Applicants\Exporter\Exporter;
use Applicants\Exporter\Json as JsonExporter;
use Applicants\Exporter\KittySay;
use Applicants\Exporter\PhpSerialize;
use Applicants\Importer\Json as JsonImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * RunCommand class.
 *
 * @package Applicants\Command
 */
class RunCommand extends Command
{

    /**
     * @var int
     */
    const EXPORT_JSON = 0;

    /**
     * @var int
     */
    const EXPORT_SERIALIZE = 1;

    /**
     * @var int
     */
    const EXPORT_COWSAY = 2;


    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('applicants:calculate')
            ->setDescription('CLI version of index.php. Have fun playing with output formats!');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $question = new Question(
            sprintf(
                'Please specify import json file (by default "%s" will be taken):',
                ($path = realpath(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', '..', 'part1', 'level6', 'data.json'))))
            ),
            $path
        );

        if (false == ($path = $helper->ask($input, $output, $question))) {
            return 1;
        }

        $importer = new JsonImporter();

        try {
            $data = $importer->import($path);
        } catch (\Exception $exception) {
            $output->write($exception->getMessage());
            return 2;
        }

        $question = new ChoiceQuestion(
            'Choose output format:',
            array(
                self::EXPORT_JSON => JsonExporter::class,
                self::EXPORT_SERIALIZE => PhpSerialize::class,
                self::EXPORT_COWSAY => KittySay::class,
            ),
            self::EXPORT_JSON
        );

        if (false == ($exporterClass = $helper->ask($input, $output, $question))) {
            return 3;
        }

        /** @var Exporter $exporter */
        $exporter = new $exporterClass;
        $output->write($exporter->export($data));

        return 0;
    }

}
