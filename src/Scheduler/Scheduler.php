<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Scheduler;

class Scheduler
{
    /**
     * @var Scheduler
     */
    protected static $current;

    /**
     * @var Loop
     */
    protected $tasks;

    /**
     * @var Task
     */
    protected $task;

    protected $ticks = 0;

    public function __construct()
    {
        $this->tasks = new Loop();
    }

    public function schedule(callable $workerLoop)
    {
        $task = new Task($workerLoop(), $this->getPriority());
        $this->tasks->enqueue($task, $task->getPriority());
    }

    public function run()
    {
        while ($this->tasks->count()) {
            $this->ticks++;
            $this->task = $this->tasks->next();

            $tickResult = ($this->task)();
            if ($tickResult) {
                $this->tasks->dequeue($this->task);
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
}