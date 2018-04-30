<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Model;

class Edge implements \JsonSerializable
{
    protected $from;

    protected $to;

    public function __construct(int $from, int $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function jsonSerialize()
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'arrows' => 'to',
            'color' => [
                'inherit' => 'both'
            ]
        ];
    }
}