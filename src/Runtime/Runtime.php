<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Runtime;

use StephanSchuler\DataStream\Model\Edge;
use StephanSchuler\DataStream\Model\Network;
use StephanSchuler\DataStream\Model\Node;
use StephanSchuler\DataStream\Provider\ProviderInterface;

class Runtime
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
     * @var RuntimeState[]
     */
    private static $states = [];

    protected function __construct(callable $definition)
    {
        $this->definition = $definition;
    }

    public static function define(callable $definition): Runtime
    {
        $className = get_called_class();
        return new $className($definition);
    }

    public static function getState(): RuntimeState
    {
        return current(self::$states);
    }

    public function addSettings(array $settings): Runtime
    {
        $this->settings = array_merge_recursive($this->settings, $settings);
        return $this;
    }

    public function run(): Runtime
    {
        foreach ($this->buildState()->getSources() as $source) {
            $source->provide();
        }
        return $this;
    }

    public function getNetwork(): Network
    {
        $state = $this->buildState();
        $workerNodes = $state->getNodes();

        $nodes = (function (RuntimeState $state, $workerNodes) {
            $nodes = [];
            foreach ($workerNodes as $id => $node) {
                $nodes[$id] = new Node($node, $id, $state->getNodeLabel($node));
            }
            return $nodes;
        })($state, $workerNodes);

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

    protected function buildState(): RuntimeState
    {
        $state = new RuntimeState($this->settings);

        array_push(self::$states, $state);

        $flow = $this->definition;
        $flow($state);

        array_pop(self::$states);

        return $state;
    }
}