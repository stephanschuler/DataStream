<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

use StephanSchuler\DataStream\Consumer\ConsumerInterface;
use StephanSchuler\DataStream\Provider\ProviderInterface;
use StephanSchuler\DataStream\Scheduler\Task;
use StephanSchuler\DataStream\Scheduler\Scheduler;

trait TransportTrait
{
    /**
     * @var ProviderInterface[]
     */
    protected $providers = [];

    /**
     * @param ProviderInterface $provider
     * @see \StephanSchuler\DataStream\Consumer\ConsumerTrait::providedBy()
     */
    public function providedBy(ProviderInterface $provider)
    {
        /** @var TransportInterface $this */
        $provider->consumedBy($this);
        $this->providers[] = $provider;
    }

    /**
     * @var ConsumerInterface[]
     * @see \StephanSchuler\DataStream\Consumer\ConsumerTrait::providedBy()
     */
    protected $consumers = [];

    /**
     * @param ConsumerInterface $consumer
     * @see \StephanSchuler\DataStream\Consumer\ConsumerTrait::providedBy()
     */
    public function consumedBy(ConsumerInterface $consumer)
    {
        $this->consumers[] = $consumer;
    }

    /**
     * @return ConsumerInterface[]
     * @see \StephanSchuler\DataStream\Consumer\ConsumerTrait::providedBy()
     */
    public function getConsumers()
    {
        return $this->consumers;
    }

    /**
     * @param $data
     * @see \StephanSchuler\DataStream\Consumer\ConsumerTrait::providedBy()
     */
    protected function feedConsumers($data)
    {
        Scheduler::globalInstance()->schedule(function () use ($data) {

            foreach (($this->getConsumers()) as $consumer) {
                yield;
                $consumer->consume($data);
            }

        });
    }
}