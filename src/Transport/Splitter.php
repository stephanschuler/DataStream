<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

class Splitter implements TransportInterface, GeneratorInterface
{
    use SplitterTrait;

    public static function createTransport(callable $definition): Splitter
    {
        $className = get_called_class();
        return new $className($definition);
    }
}