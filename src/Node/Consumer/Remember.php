<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Consumer;

use StephanSchuler\DataStream\Runtime\GraphBuilder;

class Remember implements ConsumerInterface, StatefulInterface
{
    use ConsumerTrait;

    protected $remember;

    public function __construct()
    {
        GraphBuilder::getInstance()->addNode($this);
    }

    public static function createConsumer(): Remember
    {
        $className = get_called_class();
        return new $className();
    }

    public function consume($data, $wireName = '')
    {
        $this->remember = $data;
    }

    public function get()
    {
        return $this->remember;
    }
}