<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Transport;

use StephanSchuler\DataStream\Runtime\GraphBuilder;
use StephanSchuler\DataStream\Scheduler\Scheduler;

class Merger implements TransportInterface
{
    use TransportTrait;

    public function __construct()
    {
        GraphBuilder::getInstance()->addNode($this);
    }

    public static function createTransport(): Merger
    {
        $className = get_called_class();
        return new $className();
    }

    public function consume( $data, $wireName = '')
    {
        Scheduler::globalInstance()->enqueueTask($this, function () use ($data) {

            yield;

            $this->feedConsumers($data);

        });
    }
}