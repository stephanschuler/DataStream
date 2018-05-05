<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Runtime;

use StephanSchuler\DataStream\Model\Edge;
use StephanSchuler\DataStream\Model\Network;
use StephanSchuler\DataStream\Model\Node;
use StephanSchuler\DataStream\Provider\ProviderInterface;
use StephanSchuler\DataStream\Scheduler\Scheduler;

class Process
{
    /**
     * @var callable
     */
    private $definition;

    /**
     * @var array
     */
    private $settings = [];

    /**
     * @var GraphBuilder[]
     */
    private static $builders = [];

    protected function __construct(callable $definition)
    {
        $this->definition = $definition;
    }

    public static function define(callable $definition): Process
    {
        $className = get_called_class();
        return new $className($definition);
    }

    public static function getCurrentBuilder(): GraphBuilder
    {
        return current(self::$builders);
    }

    public function addSettings(array $settings): Process
    {
        $this->settings = array_merge_recursive($this->settings, $settings);
        return $this;
    }

    /**
     * @return Process
     * @throws \Exception
     */
    public function run(): Process
    {
        Scheduler::withGlobalInstance(function () {
            foreach ($this->buildGraph()->getSources() as $source) {
                $source->provide();
            }
            Scheduler::globalInstance()->run();
        });
        return $this;
    }

    public function getNetwork(): Network
    {
        $graph = $this->buildGraph();
        $workerNodes = $graph->getNodes();

        $nodes = (function (Graph $state, $workerNodes) {
            $nodes = [];
            foreach ($workerNodes as $id => $node) {
                $nodes[$id] = new Node($node, $id, $state->getNodeLabel($node));
            }
            return $nodes;
        })($graph, $workerNodes);

        $edges = (function ($nodes, $workerNodes) {
            $edges = [];
            foreach ($workerNodes as $id => $node) {
                if ($node instanceof ProviderInterface) {
                    $fromNode = $nodes[$id];
                    foreach ($node->getConsumers() as $consumer) {
                        $toNode = $nodes[array_search($consumer, $workerNodes)];
                        $edges[] = new Edge($fromNode, $toNode);
                    }
                }
            }
            return $edges;
        })($nodes, $workerNodes);

        return new Network($nodes, $edges);
    }

    protected function buildGraph(): Graph
    {
        $graph = new Graph();
        $builder = new GraphBuilder($graph, $this->settings);

        array_push(self::$builders, $builder);

        $flow = $this->definition;
        $flow($builder);

        array_pop(self::$builders);

        return $graph;
    }
}