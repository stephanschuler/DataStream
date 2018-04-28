<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Consumer;

use StephanSchuler\DataStream\Provider\ProviderInterface;

class Echoing implements ConsumerInterface
{
    const TEMPLATE = '
<h1>%s</h1>
<pre>%s</pre>
';

    protected $name;

    protected function __construct(string $name)
    {
        $this->name = $name;
    }

    public function providedBy(ProviderInterface $provider)
    {
        /** @var ConsumerInterface $this */
        $provider->consumedBy($this);
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