<?PHP

$autoloader = require_once __DIR__ . '/../../vendor/autoload.php';

/** @var \StephanSchuler\DataStream\Runtime\Process $process */
$process = require(__DIR__ . '/data/process.php');

$process->addSettings([
    'filename' => __DIR__ . '/data/products.xml',
]);

$network = $process->getNetwork();

foreach ($network->getNodes() as $i => $node) {
    $node->setLayout([
        'x' => $i * 100,
        'fixed' => [
            'x' => true,
        ]
    ]);
}

$template = file_get_contents(__DIR__ . '/data/template.html');

echo str_replace('{{network}}', json_encode($network), $template);