<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Model;

use StephanSchuler\DataStream\Consumer\ConsumerInterface;
use StephanSchuler\DataStream\Node\NodeInterface;
use StephanSchuler\DataStream\Provider\ProviderInterface;

class Node implements \JsonSerializable
{
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

        if ($node instanceof ProviderInterface && $node instanceof ConsumerInterface) {
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

    public function jsonSerialize()
    {
        return array_merge($this->getLayout(), [
            'id' => (string)$this->getId(),
            'label' => $this->getLabel(),
        ]);
    }
}