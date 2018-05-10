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
     * @var Task[]
     */
    protected $tasks = [];

    /**
     * @var Task
     */
    protected $task;

    protected $ticks = 0;

    /**
     * @var DependencyCalculator
     */
    protected $dependencies;

    public function __construct()
    {
        $this->dependencies;
    }

    public function enqueueTask(NodeInterface $source, callable $workerLoop)
    {
        $task = new Task($source, $workerLoop(), $this->getPriority());
        $this->tasks[] = $task;
    }

    public function run()
    {
        while (count($this->tasks)) {

            $this->task = $this->getNextPossibleTask();

            $this->ticks++;

            $tickResult = ($this->task)();
            if ($tickResult) {
                $this->tasks = array_filter($this->tasks, function (Task $task) {
                    return $task !== $this->task;
                });
            }
        }
    }

    public static function withGlobalInstance(DependencyCalculator $dependencies, callable $callback)
    {
        $before = Scheduler::$current;

        $class = get_called_class();
        Scheduler::$current = new $class;
        Scheduler::$current->dependencies = $dependencies;
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

    protected function getNextPossibleTask()
    {
        $possibleTasks = [];
        foreach (array_values($this->tasks) as $askingTask) {
            if (!$this->isBlockedByDependencies($askingTask)) {
                $possibleTasks[] = $askingTask;
            }
        }
        usort($possibleTasks, function (Task $a, Task $b) {
            return $a->getPriority() <=> $b->getPriority();
        });
        return current($possibleTasks);
    }

    protected function isBlockedByDependencies(Task $askingTask): bool
    {
        $askingNode = $askingTask->getSource();

        $waitingTasks = array_filter($this->tasks, function (Task $waitingTask) use ($askingTask) {
            return $waitingTask !== $askingTask;
        });
        /** @var NodeInterface[] $waitingNodes */
        $waitingNodes = array_map(function (Task $task) {
            return $task->getSource();
        }, $waitingTasks);

        return $this->dependencies->isBlockedByDependencies($askingNode, ...$waitingNodes);
    }
}