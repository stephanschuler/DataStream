<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Model;

use StephanSchuler\DataStream\Provider\ProviderInterface;

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
        $this->nodes = self::sortNodes(...$nodes);
        $this->edges = self::sortEdges(...$edges);
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

    protected static function sortNodes(Node ...$nodes)
    {
        $nodesByType = [
            Node::TYPE_PROVIDER => [],
            Node::TYPE_TRANSPORT => [],
            Node::TYPE_CONSUMER => [],
            Node::TYPE_UNDEFINED => [],
        ];
        foreach ($nodes as $node) {
            $nodesByType[$node->getType()][] = $node;
        }
        array_walk($nodesByType, function(&$nodes) {
            usort($nodes, function(Node $a, Node $b) {
                return $b->getWeight() <=> $a->getWeight();
            });
        });
        return array_merge(...array_values($nodesByType));
    }

    protected static function sortEdges(Edge ...$edges)
    {
        return $edges;
    }
}