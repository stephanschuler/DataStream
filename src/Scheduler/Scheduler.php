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
            $this->task = $this->tasks->next();

            $tickResult = ($this->task)();
            if ($tickResult) {
                $this->tasks->dequeue($this->task);
            }

            $this->task = null;
        }
    }

    public static function withScheduler(callable $callback)
    {
        if (Scheduler::$current) {
            throw new \Exception('There can only be');
        }

        $class = get_called_class();
        Scheduler::$current = new $class;
        $callback();
        Scheduler::$current = null;
    }

    public static function current()
    {
        return self::$current;
    }

    protected function getPriority(): int
    {
        return $this->task ? $this->task->getPriority() + 1 : 1;
    }
}