<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

use StephanSchuler\DataStream\Runtime\StateBuilder;
use StephanSchuler\DataStream\Scheduler\Scheduler;

class XpathMapper implements TransportInterface
{
    use TransportTrait;

    /**
     * @var array
     */
    protected $definition;

    protected function __construct(array $definition)
    {
        StateBuilder::getInstance()->addNode($this);
        $this->definition = $definition;
    }

    public function consume($data)
    {
        Scheduler::current()->schedule(function () use ($data) {

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