<?PHP

$autoloader = require_once __DIR__ . '/../../vendor/autoload.php';
$autoloader->addPsr4('StephanSchuler\\DataStream\\', __DIR__ . '/src');

/** @var \StephanSchuler\DataStream\Runtime\Runtime $runtime */
$runtime = require(__DIR__ . '/data/runtime.php');

$runtime->addSettings([
    'filename' => __DIR__ . '/data/products.xml',
]);

$network = $runtime->getNetwork();

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