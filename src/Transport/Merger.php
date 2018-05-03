<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

use StephanSchuler\DataStream\Runtime\RuntimeState;

class Merger implements TransportInterface
{
    use TransportTrait;

    protected function __construct()
    {
        RuntimeState::getInstance()->addNode($this);
    }

    public static function createTransport(): Merger
    {
        $className = get_called_class();
        return new $className();
    }

    public function consume($data)
    {
        $this->feedConsumers($data);
    }
}