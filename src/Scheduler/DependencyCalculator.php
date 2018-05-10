<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Scheduler;

use StephanSchuler\DataStream\Node\NodeInterface;
use StephanSchuler\DataStream\Node\Provider\ProviderInterface;

class DependencyCalculator
{
    const EDGE_FROM = 0;
    const EDGE_TO = 1;

    protected $dependencies;

    public function __construct(NodeInterface ...$nodes)
    {
        $this->dependencies = $this->calculateDependencies(...$nodes);
    }

    public function isBlockedByDependencies(NodeInterface $askingNode, NodeInterface ...$waitingNodes): bool
    {
        foreach ($waitingNodes as $waitingNode) {
            if (in_array($askingNode, $this->dependencies->offsetGet($waitingNode))) {
                return true;
            }
        }

        return false;
    }

    protected function calculateDependencies(NodeInterface ...$nodes)
    {
        $edges = $this->getEdges(...$nodes);

        $dependencies = [];
        foreach (array_keys($nodes) as $nodeId) {
            $this->getDependencyFor($nodeId, $edges, $dependencies);
        }

        $result = new \SplObjectStorage();
        foreach ($dependencies as $id => $dependsOn) {
            $result[$nodes[$id]] = array_map(function ($id) use ($nodes) {
                return $nodes[$id];
            }, $dependsOn);
        }

        return $result;
    }

    protected function getEdges(NodeInterface ...$nodes)
    {
        $edges = [];
        foreach ($nodes as $fromId => $fromNode) {
            if ($fromNode instanceof ProviderInterface) {
                foreach ($fromNode->getConsumers() as $toNode) {
                    $toId = array_search($toNode, $nodes, true);
                    $edges[] = [
                        self::EDGE_FROM => $fromId,
                        self::EDGE_TO => $toId,
                    ];
                }
            }
        }
        return $edges;
    }

    protected function getDependencyFor(int $id, array $edges, array &$dependencies)
    {
        if (array_key_exists($id, $dependencies)) {
            return $dependencies[$id];
        }

        $dependsOn = [];
        foreach ($edges as $edge) {
            if ($id === $edge[self::EDGE_TO]) {
                $dependsOn[] = $edge[self::EDGE_FROM];
            }
        }

        $dependencies[$id] = array_reduce($dependsOn, function ($curry, $id) use ($edges, &$dependencies) {
            return array_merge($curry, [$id], $this->getDependencyFor($id, $edges, $dependencies));
        }, []);
        return $dependencies[$id];
    }
}