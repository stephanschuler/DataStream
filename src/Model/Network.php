<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Model;

class Network implements \JsonSerializable
{
    /**
     * @var Node[]
     */
    private $nodes;

    /**
     * @var Edge[]
     */
    private $edges;

    public function __construct(array $nodes, array $edges)
    {
        $this->nodes = $nodes;
        $this->edges = $edges;
    }

    public function setLayoutForType(string $type, array $layout)
    {
        $nodes = array_filter($this->nodes, function (Node $node) use ($type) {
            return $node->getType() === $type;
        });
        array_walk($nodes, function (Node $node) use ($layout) {
            $node->setLayout($layout);
        });
    }

    public function getNodes()
    {
        return $this->nodes;
    }

    public function getEdges()
    {
        return $this->edges;
    }

    public function jsonSerialize()
    {
        return [
            'nodes' => $this->getNodes(),
            'edges' => $this->getEdges()
        ];
    }
}