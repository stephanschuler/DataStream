<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Provider;

use StephanSchuler\DataStream\Node\Consumer\ConsumerInterface;
use StephanSchuler\DataStream\Scheduler\Scheduler;

trait ProviderTrait
{
    /**
     * @var ConsumerInterface[]
     */
    protected $consumers = [];

    public function consumedBy(ConsumerInterface $consumer, string $wireName = '')
    {
        if ($wireName) {
            $this->consumers[$wireName] = $consumer;
        } else {
            $this->consumers[] = $consumer;
        }
    }

    /**
     * @return ConsumerInterface[]
     */
    public function getConsumers()
    {
        return $this->consumers;
    }

    protected function feedConsumers($data)
    {
        Scheduler::globalInstance()->enqueueConsumingTask($this, function () use ($data) {
            foreach ($this->getConsumers() as $wireName => $consumer) {
                yield;
                $consumer->consume($data, $wireName);
            }
        });
    }
}