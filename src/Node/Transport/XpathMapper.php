<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Transport;

use StephanSchuler\DataStream\Runtime\GraphBuilder;
use StephanSchuler\DependencyScheduler\Scheduler;

class XpathMapper implements TransportInterface
{
    use TransportTrait;

    /**
     * @var array
     */
    protected $definition;

    public function __construct(array $definition)
    {
        GraphBuilder::getInstance()->addNode($this);
        $this->definition = $definition;
    }

    public function consume($data, $wireName = '')
    {
        Scheduler::globalInstance()->enqueueWorkload($this, function () use ($data) {

            yield;

            /** @var \SimpleXMLElement $data */
            $newData = array_map(function ($xpath) use ($data) {
                return (string)current($data->xpath($xpath));
            }, $this->definition);
            $this->feedConsumers($newData);

        });
    }

    public static function createTransport(array $definition): XpathMapper
    {
        $className = get_called_class();
        return new $className($definition);
    }
}