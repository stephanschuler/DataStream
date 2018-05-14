<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Transport;

use StephanSchuler\DataStream\Node\NodeInterface;
use StephanSchuler\DataStream\Runtime\GraphBuilder;
use StephanSchuler\DependencyScheduler\Scheduler;

trait SplitterTrait
{
    use TransportTrait;

    /**
     * @var \callable
     */
    protected $definition;

    public function __construct(callable $definition)
    {
        /** @var NodeInterface $this */
        GraphBuilder::getInstance()->addNode($this);
        $this->definition = $definition;
    }

    public function consume($data, $wireName = '')
    {
        /** @var NodeInterface $this */
        Scheduler::globalInstance()->enqueueWorkload($this, function () use ($data) {

            $generator = ($this->definition)($data);
            foreach ($generator as $partData) {
                yield;
                $this->feedConsumers($partData);
            }

        });
    }

}