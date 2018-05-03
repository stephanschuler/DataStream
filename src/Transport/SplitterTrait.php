<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

use StephanSchuler\DataStream\Runtime\RuntimeState;

trait SplitterTrait
{
    use TransportTrait;

    /**
     * @var \callable
     */
    protected $definition;

    protected function __construct(callable $definition)
    {
        RuntimeState::getInstance()->addNode($this);
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