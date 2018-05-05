<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

use StephanSchuler\DataStream\Runtime\StateBuilder;
use StephanSchuler\DataStream\Scheduler\Task;
use StephanSchuler\DataStream\Scheduler\Scheduler;

class Merger implements TransportInterface
{
    use TransportTrait;

    protected function __construct()
    {
        StateBuilder::getInstance()->addNode($this);
    }

    public static function createTransport(): Merger
    {
        $className = get_called_class();
        return new $className();
    }

    public function consume($data)
    {
        Scheduler::current()->schedule(function () use ($data) {

            yield;

            $this->feedConsumers($data);

        });
    }
}