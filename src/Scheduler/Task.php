<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Scheduler;

use Generator;
use StephanSchuler\DataStream\Node\NodeInterface;

class Task
{
    /**
     * @var NodeInterface
     */
    protected $source;

    /**
     * @var Generator
     */
    protected $loop;

    /**
     * @var int
     */
    protected $priority;

    public function __construct(NodeInterface $source, Generator $loop, int $priority)
    {
        $this->source = $source;
        $this->loop = $loop;
        $this->priority = $priority;
    }

    public function getSource(): NodeInterface
    {
        return $this->source;
    }

    public function __invoke(): bool
    {
        $this->loop->current();
        $this->loop->next();
        return !$this->loop->valid();
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}