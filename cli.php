#!/usr/bin/env php
<?php

use Applicants\Command\RunCommand;
use Symfony\Component\Console\Application;

require_once 'vendor/autoload.php';

$application = new Application();
$application->add(new RunCommand());

$application->run();
