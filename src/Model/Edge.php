<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Model;

class Edge implements \JsonSerializable
{
    const UNNAMED_LABEL = '?';

    protected $from;

    protected $to;

    protected $wireName;

    protected $priority;

    public function __construct(Node $from, Node $to, string $wireName, int $priority)
    {
        $this->from = $from;
        $this->to = $to;
        $this->wireName = $wireName;
        $this->priority = $priority;
    }

    public function getFrom(): Node
    {
        return $this->from;
    }

    public function getTo(): Node
    {
        return $this->to;
    }

    public function getWireName(): string
    {
        if ((string)(int)$this->wireName === $this->wireName) {
            return self::UNNAMED_LABEL;
        } else {
            return $this->wireName;
        }
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function jsonSerialize()
    {
        return [
            'from' => $this->getFrom()->getId(),
            'to' => $this->getTo()->getId(),
            'arrows' => 'to',
            'label' => sprintf('%s [%d]', $this->getWireName(), $this->getPriority()),
            'color' => [
                'inherit' => 'both'
            ]
        ];
    }
}