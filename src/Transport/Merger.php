<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

class Merger implements TransportInterface
{
    use TransportTrait;

    protected function __construct()
    {
    }

    public static function createTransport(): Merger
    {
        $className = get_called_class();
        return new $className();
    }

    public function consume($data)
    {
        foreach ($this->consumers as $consumer) {
            $consumer->consume($data);
        }
    }
}