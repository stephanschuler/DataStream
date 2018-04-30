<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Consumer;

use StephanSchuler\DataStream\Runtime\RuntimeState;

class Echoing implements ConsumerInterface
{
    use ConsumerTrait;

    const TEMPLATE = '
<h1>%s</h1>
<pre>%s</pre>
';

    protected $name;

    protected function __construct(string $name)
    {
        RuntimeState::getInstance()->addNode($this);
        $this->name = $name;
    }

    public static function createConsumer(string $name = 'default'): Echoing
    {
        $className = get_called_class();
        return new $className($name);
    }

    public function consume($data)
    {
        echo sprintf(self::TEMPLATE, $this->name, htmlspecialchars(print_r($data, true)));
    }
}