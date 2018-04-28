<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

class Mapper implements TransportInterface
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
        $newData = ($this->definition)($data);
        foreach ($this->consumers as $consumer) {
            $consumer->consume($newData);
        }
    }

    public static function createTransport(callable $definition): Mapper
    {
        $className = get_called_class();
        return new $className($definition);
    }

}