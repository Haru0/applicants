<?php

use Applicants\Calculator;
use Applicants\Importer\Json;

require_once 'vendor/autoload.php';

$importer = new Json();

try {
    $data = $importer->import(implode(DIRECTORY_SEPARATOR, array(__DIR__, 'part1', 'level1', 'data.json')));
} catch (\Exception $exception) {
    die($exception->getMessage());
}

$calculator = new Calculator();
$output = $calculator->calculate($data['users'], $data['providers']);

header('Content-type: application/json');
header('Content-Disposition: attachment; filename="output.json"');

echo json_encode($output);
exit;
