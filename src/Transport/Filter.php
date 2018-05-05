<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

use StephanSchuler\DataStream\Runtime\StateBuilder;
use StephanSchuler\DataStream\Scheduler\Scheduler;

class Filter implements TransportInterface, EliminatorInterface
{
    use TransportTrait;

    /**
     * @var \callable
     */
    protected $definition;

    protected function __construct(callable $definition)
    {
        StateBuilder::getInstance()->addNode($this);
        $this->definition = $definition;
    }

    public function consume($data)
    {
        Scheduler::current()->schedule(function () use ($data) {

            yield;

            if (($this->definition)($data)) {
                $this->feedConsumers($data);
            }

        });
    }

    public static function createTransport(callable $definition): Filter
    {
        $className = get_called_class();
        return new $className($definition);
    }
}