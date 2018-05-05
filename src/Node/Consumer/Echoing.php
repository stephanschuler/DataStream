<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Consumer;

use StephanSchuler\DataStream\Runtime\GraphBuilder;

class Echoing implements ConsumerInterface
{
    use ConsumerTrait;

    const TEMPLATE = '
<h1>%s (%s)</h1>
<pre>%s</pre>
';

    protected $name;

    protected function __construct(string $name)
    {
        GraphBuilder::getInstance()->addNode($this);
        $this->name = $name;
    }

    public static function createConsumer(string $name = 'default'): Echoing
    {
        $className = get_called_class();
        return new $className($name);
    }

    public function consume($data, $wireName = '')
    {
        echo sprintf(self::TEMPLATE, $this->name, htmlspecialchars((string)$wireName), htmlspecialchars(print_r($data, true)));
    }
}