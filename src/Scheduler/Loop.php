<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Scheduler;

class Loop
{
    /**
     * @var mixed[][]
     */
    protected $queues = [];

    protected $numberOfItems = 0;

    public function next()
    {
        $priority = key($this->queues);
        $item = array_shift($this->queues[$priority]);
        array_push($this->queues[$priority], $item);
        return $item;
    }

    public function enqueue($item, int $priority = 0)
    {
        $this->dequeue($item);
        if (!array_key_exists($priority, $this->queues)) {
            $this->queues[$priority] = [];
            krsort($this->queues);
        }
        array_push($this->queues[$priority], $item);
        $this->numberOfItems++;
    }

    public function dequeue($item)
    {
        $this->numberOfItems = 0;
        foreach ($this->queues as $priority => $queue) {
            $this->queues[$priority] = array_filter($queue, function ($existing) use ($item) {
                return $item !== $existing;
            });
            $count = count($this->queues[$priority]);
            if ($count) {
                $this->numberOfItems += $count;
            } else {
                unset($this->queues[$priority]);
            }
        }
    }

    public function count()
    {
        return $this->numberOfItems;
    }
}