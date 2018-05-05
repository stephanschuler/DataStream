<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Scheduler;

use Generator;

class Task
{
    /**
     * @var Generator
     */
    protected $loop;

    /**
     * @var int
     */
    protected $priority;

    public function __construct(Generator $loop, int $priority)
    {
        $this->loop = $loop;
        $this->priority = $priority;
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