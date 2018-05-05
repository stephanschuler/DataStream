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
     * @var Task[]
     */
    protected $tasks = [];

    /**
     * @var Task
     */
    protected $task;

    public function schedule(callable $workerLoop)
    {
        $this->tasks[] = new Task($workerLoop(), $this->getPriority());
    }

    public function run()
    {
        $ticks = 0;
        $executed = 0;

        while ($this->tasks) {
            $ticks++;
            $this->task = $this->next();

            if ($this->hasBestPriority()) {
                $tickResult = ($this->task)();
                $executed++;
                if ($tickResult) {
                    $this->tasks = array_filter($this->tasks, function ($worker) {
                        return $worker !== $this->task;
                    });
                }
            }

            $this->task = null;
        }
        print_r([
            'ticks' => $ticks,
            'executed' => $executed,
            'memory_get_peak_usage' => memory_get_peak_usage()
        ]);
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

    protected function next(): Task
    {
        $current = array_shift($this->tasks);
        array_push($this->tasks, $current);
        return $current;
    }

    protected function hasBestPriority(): bool
    {
        return !array_filter($this->tasks, function (Task $worker) {
            return $worker->getPriority() > $this->task->getPriority();
        });
    }

    protected function getPriority(): int
    {
        return $this->task ? $this->task->getPriority() + 1 : 1;
    }
}