<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

use StephanSchuler\DataStream\Runtime\GraphBuilder;
use StephanSchuler\DataStream\Scheduler\Task;
use StephanSchuler\DataStream\Scheduler\Scheduler;

trait SplitterTrait
{
    use TransportTrait;

    /**
     * @var \callable
     */
    protected $definition;

    protected function __construct(callable $definition)
    {
        GraphBuilder::getInstance()->addNode($this);
        $this->definition = $definition;
    }

    public function consume($data)
    {
        Scheduler::globalInstance()->schedule(function () use ($data) {

            $generator = ($this->definition)($data);
            foreach ($generator as $partData) {
                yield;
                $this->feedConsumers($partData);
            }

        });
    }

}