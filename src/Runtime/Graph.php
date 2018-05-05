<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Runtime;

use StephanSchuler\DataStream\Node\NodeInterface;
use StephanSchuler\DataStream\Node\Source\SourceInterface;

class Graph
{
    /**
     * @var SourceInterface[]
     */
    private $sources = [];

    /**
     * @var NodeInterface[]
     */
    private $nodes = [];

    /**
     * @var string[]
     */
    private $labels = [];

    public function getSources()
    {
        return $this->sources;
    }

    public function setSource(SourceInterface ...$sources): Graph
    {
        $this->sources = $sources;
        return $this;
    }

    public function getNodes()
    {
        return $this->nodes;
    }

    public function addNode(NodeInterface $node): Graph
    {
        $this->nodes[] = $node;
        if ($node instanceof SourceInterface) {
            $this->sources[] = $node;
        }
        return $this;
    }

    public function getNodeLabel(NodeInterface $node): string
    {
        $hash = spl_object_hash($node);
        if (isset($this->labels[$hash])) {
            return $this->labels[$hash];
        } else {
            $label = explode('\\', get_class($node));
            return end($label);
        }
    }

    public function setNodeLabel(NodeInterface $node, string $label): Graph
    {
        $this->labels[spl_object_hash($node)] = $label;
        return $this;
    }

}