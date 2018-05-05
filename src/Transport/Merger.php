<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

use StephanSchuler\DataStream\Runtime\GraphBuilder;
use StephanSchuler\DataStream\Scheduler\Task;
use StephanSchuler\DataStream\Scheduler\Scheduler;

class Merger implements TransportInterface
{
    use TransportTrait;

    protected function __construct()
    {
        GraphBuilder::getInstance()->addNode($this);
    }

    public static function createTransport(): Merger
    {
        $className = get_called_class();
        return new $className();
    }

    public function consume($data)
    {
        Scheduler::globalInstance()->schedule(function () use ($data) {

            yield;

            $this->feedConsumers($data);

        });
    }
}