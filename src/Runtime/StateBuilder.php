<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Runtime;

use StephanSchuler\DataStream\Node\NodeInterface;
use StephanSchuler\DataStream\Source\SourceInterface;

class StateBuilder
{
    /**
     * @var array
     */
    private $settings = [];

    /**
     * @var State
     */
    private $state;

    public function __construct(State $state, array $settings = [])
    {
        $this->state = $state;
        $this->settings = $settings;
    }

    public static function getInstance(): StateBuilder
    {
        return Runtime::getCurrentBuilder();
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function getSetting($name)
    {
        return $this->settings[$name];
    }

    public function setSource(SourceInterface ...$sources): StateBuilder
    {
        $this->state->setSource(...$sources);
        return $this;
    }

    public function addNode(NodeInterface $node): StateBuilder
    {
        $this->state->addNode($node);
        return $this;
    }

    public function setNodeLabel(NodeInterface $node, string $label): StateBuilder
    {
        $this->state->setNodeLabel($node, $label);
        return $this;
    }

}