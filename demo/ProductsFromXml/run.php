<?php

$autoloader = require_once __DIR__ . '/../../vendor/autoload.php';
$autoloader->addPsr4('StephanSchuler\\DataStream\\', __DIR__ . '/src');

/** @var \StephanSchuler\DataStream\Runtime\Process $process */
$process = require(__DIR__ . '/data/process.php');

$process->addSettings([
    'filename' => __DIR__ . '/data/products.xml',
]);

$process->run();