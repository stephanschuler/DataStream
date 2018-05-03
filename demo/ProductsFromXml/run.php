<?php

$autoloader = require_once __DIR__ . '/../../vendor/autoload.php';
$autoloader->addPsr4('StephanSchuler\\DataStream\\', __DIR__ . '/src');

/** @var \StephanSchuler\DataStream\Runtime\Runtime $runtime */
$runtime = require(__DIR__ . '/data/runtime.php');

$runtime->addSettings([
    'filename' => __DIR__ . '/data/products.xml',
]);

$runtime->run();