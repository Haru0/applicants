<?php

use Applicants\Calculator;
use Applicants\Exporter\Json as JsonExporter;
use Applicants\Importer\Json as JsonImporter;

require_once 'vendor/autoload.php';

$importer = new JsonImporter();

try {
    $data = $importer->import(implode(DIRECTORY_SEPARATOR, array(__DIR__, 'part1', 'level6', 'data.json')));
} catch (\Exception $exception) {
    die($exception->getMessage());
}

$context = new Calculator\Context($data['users'], $data['providers']);
$context
    ->setContracts($data['contracts'])
    ->setContractModifications($data['contract_modifications']);

$calculator = new Calculator();
$output = $calculator->calculate($context);

header('Content-type: application/json');
header('Content-Disposition: attachment; filename="output.json"');

$exporter = new JsonExporter();
echo $exporter->export($output);
exit;
