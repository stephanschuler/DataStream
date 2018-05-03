<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

use StephanSchuler\DataStream\Runtime\StateBuilder;

trait SplitterTrait
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
        $generator = ($this->definition)($data);
        foreach ($generator as $partData) {
            $this->feedConsumers($partData);
        }
    }

}