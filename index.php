<?php

use Applicants\Calculator;
use Applicants\Importer\Json;

require_once 'vendor/autoload.php';

$importer = new Json();

try {
    $data = $importer->import(implode(DIRECTORY_SEPARATOR, array(__DIR__, 'part1', 'level4', 'data.json')));
} catch (\Exception $exception) {
    die($exception->getMessage());
}

$context = new Calculator\Context($data['users'], $data['providers']);
$context->setContracts($data['contracts']);

$calculator = new Calculator();
$output = $calculator->calculate($context);

header('Content-type: application/json');
header('Content-Disposition: attachment; filename="output.json"');

echo json_encode($output);
exit;
