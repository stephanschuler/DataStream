<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Consumer;

use StephanSchuler\DataStream\Runtime\RuntimeState;

class Remember implements ConsumerInterface, StatefulInterface
{
    use ConsumerTrait;

    protected $remember;

    protected function __construct()
    {
        RuntimeState::getInstance()->addNode($this);
    }

    public static function createConsumer(): Remember
    {
        $className = get_called_class();
        return new $className();
    }

    public function consume($data)
    {
        $this->remember = $data;
    }

    public function get()
    {
        return $this->remember;
    }
}