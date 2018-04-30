<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

use StephanSchuler\DataStream\Consumer\StatefulInterface;
use StephanSchuler\DataStream\Runtime\RuntimeState;

class DistinctUntilChanged implements TransportInterface, StatefulInterface
{
    use TransportTrait;

    protected $lastItem;

    /**
     * @var callable
     */
    protected $compare;

    protected function __construct(callable $compare = null)
    {
        RuntimeState::getInstance()->addNode($this);
        $this->compare = $compare;
    }

    public function consume($data)
    {
        $comparable = $this->compare ? ($this->compare)($data) : $data;
        if ($comparable === $this->lastItem) {
            return;
        }
        $this->lastItem = $comparable;
        foreach ($this->consumers as $consumer) {
            $consumer->consume($data);
        }
    }

    public function get()
    {
        return $this->lastItem;
    }

    public static function createTransport(): DistinctUntilChanged
    {
        $className = get_called_class();
        return new $className();
    }

    public static function createTransportWithCompareFunction(callable $compare): DistinctUntilChanged
    {
        $className = get_called_class();
        return new $className($compare);
    }
}