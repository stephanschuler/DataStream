<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Scheduler;

use StephanSchuler\DataStream\Node\NodeInterface;

class Scheduler
{
    /**
     * @var Scheduler
     */
    protected static $current;

    /**
     * @var Loop
     */
    protected $producingTasks;

    /**
     * @var Loop
     */
    protected $consumingTasks;

    /**
     * @var Task
     */
    protected $task;

    protected $ticks = 0;

    public function __construct()
    {
        $this->consumingTasks = new Loop();
        $this->producingTasks = new Loop();
    }

    public function enqueueConsumingTask(NodeInterface $source, callable $workerLoop)
    {
        $task = new Task($source, $workerLoop(), $this->getPriority());
        $this->consumingTasks->enqueue($task, $task->getPriority());
    }

    public function enqueueProducingTask(NodeInterface $source, callable $workerLoop)
    {
        $task = new Task($source, $workerLoop(), $this->getPriority());
        $this->producingTasks->enqueue($task, $task->getPriority());
    }

    public function run()
    {
        while ($loop = $this->getActiveLoop()) {

            $this->ticks++;
            $this->task = $loop->next();

            $tickResult = ($this->task)();
            if ($tickResult) {
                $loop->dequeue($this->task);
            }

            $this->task = null;
        }
    }

    public static function withGlobalInstance(callable $callback)
    {
        $before = Scheduler::$current;

        $class = get_called_class();
        Scheduler::$current = new $class;
        $callback();
        Scheduler::$current = $before;
    }

    public static function globalInstance(): Scheduler
    {
        return self::$current;
    }

    public function getTicks()
    {
        return $this->ticks;
    }

    protected function getPriority(): int
    {
        return ($this->task ? $this->task->getPriority() : 0) + 1;
    }

    protected function getActiveLoop()
    {
        if ($this->consumingTasks->count()) {
            return $this->consumingTasks;
        } elseif ($this->producingTasks->count()) {
            return $this->producingTasks;
        }
    }
}