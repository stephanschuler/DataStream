<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Source;

use ArrayIterator;
use Iterator;
use StephanSchuler\DataStream\Node\Provider\ProviderTrait;
use StephanSchuler\DataStream\Runtime\GraphBuilder;
use StephanSchuler\DataStream\Scheduler\Scheduler;

class Sequence implements SourceInterface
{
    use ProviderTrait;

    /**
     * @var Iterator
     */
    protected $source;

    protected function __construct(Iterator $source)
    {
        GraphBuilder::getInstance()->addNode($this);
        $this->source = $source;
    }

    public function provide()
    {
        Scheduler::globalInstance()->enqueueProducingTask($this, function () {

            foreach ($this->source as $element) {
                yield;
                $this->feedConsumers($element);
            }

        });
    }

    public static function createSource(Iterator $source): Sequence
    {
        $className = get_called_class();
        return new $className($source);
    }

    public static function createSourceFromArray(array $source): Sequence
    {
        $callable = [get_called_class(), 'createSource'];
        return $callable(new ArrayIterator($source));
    }

    public static function createSourceOf(...$source): Sequence
    {
        $callable = [get_called_class(), 'createSourceFromArray'];
        return $callable($source);
    }
}