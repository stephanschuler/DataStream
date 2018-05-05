<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Model;

use StephanSchuler\DataStream\Node\Consumer\ConsumerInterface;
use StephanSchuler\DataStream\Node\NodeInterface;
use StephanSchuler\DataStream\Node\Provider\ProviderInterface;
use StephanSchuler\DataStream\Node\Source\SourceInterface;
use StephanSchuler\DataStream\Node\Transport\EliminatorInterface;
use StephanSchuler\DataStream\Node\Transport\GeneratorInterface;
use StephanSchuler\DataStream\Node\Transport\TransportInterface;

class Node implements \JsonSerializable
{
    const TYPE_SOURCE = 'source';

    const TYPE_TRANSPORT = 'transport';

    const TYPE_PROVIDER = 'provider';

    const TYPE_CONSUMER = 'consumer';

    const TYPE_UNDEFINED = 'undefined';

    /**
     * @var NodeInterface
     */
    protected $node;

    protected $type;

    protected $id;

    protected $label;

    protected $layout = [];

    public function __construct(NodeInterface $node, int $id, string $label)
    {
        $this->node = $node;
        $this->id = $id;

        if ($node instanceof SourceInterface) {
            $this->type = self::TYPE_SOURCE;
        } elseif ($node instanceof TransportInterface) {
            $this->type = self::TYPE_TRANSPORT;
        } elseif ($node instanceof ProviderInterface) {
            $this->type = self::TYPE_PROVIDER;
        } elseif ($node instanceof ConsumerInterface) {
            $this->type = self::TYPE_CONSUMER;
        } else {
            $this->type = self::TYPE_UNDEFINED;
        }

        $this->label = $label;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function setLayout(array $color)
    {
        $this->layout = $color;
    }

    public function getWeight()
    {
        $weight = 1;
        if ($this->node instanceof GeneratorInterface) {
            $weight += 5;
        }
        if ($this->node instanceof EliminatorInterface) {
            $weight -= 5;
        }
        return $weight;
    }

    public function jsonSerialize()
    {
        $nodesByType = [
            Node::TYPE_SOURCE,
            Node::TYPE_PROVIDER,
            Node::TYPE_TRANSPORT,
            Node::TYPE_CONSUMER,
            Node::TYPE_UNDEFINED,
        ];
        return array_merge($this->getLayout(), [
            'id' => (string)$this->getId(),
            'label' => $this->getLabel(),
            'group' => $this->getType(),
            'level' => array_search($this->getType(), $nodesByType)
        ]);
    }
}