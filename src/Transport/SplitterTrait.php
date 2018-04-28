<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

trait SplitterTrait
{
    use TransportTrait;

    /**
     * @var \callable
     */
    protected $definition;

    protected function __construct(callable $definition)
    {
        $this->definition = $definition;
    }

    public function consume($data)
    {
        $generator = ($this->definition)($data);
        foreach ($generator as $partData) {
            foreach ($this->consumers as $consumer) {
                $consumer->consume($partData);
            }
        }
    }

}