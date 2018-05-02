<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Model;

class Edge implements \JsonSerializable
{
    protected $from;

    protected $to;

    public function __construct(Node $from, Node $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function getFrom(): Node
    {
        return $this->from;
    }

    public function getTo(): Node
    {
        return $this->to;
    }

    public function jsonSerialize()
    {
        return [
            'from' => $this->getFrom()->getId(),
            'to' => $this->getTo()->getId(),
            'arrows' => 'to',
            'color' => [
                'inherit' => 'both'
            ]
        ];
    }
}