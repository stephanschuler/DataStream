<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Runtime;

use StephanSchuler\DataStream\Node\NodeInterface;
use StephanSchuler\DataStream\Source\SourceInterface;

class GraphBuilder
{
    /**
     * @var array
     */
    private $settings = [];

    /**
     * @var Graph
     */
    private $graph;

    public function __construct(Graph $graph, array $settings = [])
    {
        $this->graph = $graph;
        $this->settings = $settings;
    }

    public static function getInstance(): GraphBuilder
    {
        return Process::getCurrentBuilder();
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function getSetting($name)
    {
        return $this->settings[$name];
    }

    public function setSource(SourceInterface ...$sources): GraphBuilder
    {
        $this->graph->setSource(...$sources);
        return $this;
    }

    public function addNode(NodeInterface $node): GraphBuilder
    {
        $this->graph->addNode($node);
        return $this;
    }

    public function setNodeLabel(NodeInterface $node, string $label): GraphBuilder
    {
        $this->graph->setNodeLabel($node, $label);
        return $this;
    }

}