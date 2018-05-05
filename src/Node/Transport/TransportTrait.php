<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Transport;

use StephanSchuler\DataStream\Node\Consumer\ConsumerInterface;
use StephanSchuler\DataStream\Node\Provider\ProviderInterface;
use StephanSchuler\DataStream\Scheduler\Scheduler;

trait TransportTrait
{
    /**
     * @var ProviderInterface[]
     */
    protected $providers = [];

    /**
     * @param ProviderInterface $provider
     * @param string $wireName
     * @see \StephanSchuler\DataStream\Node\Consumer\ConsumerTrait::providedBy()
     */
    public function providedBy(ProviderInterface $provider, string $wireName = '')
    {
        /** @var TransportInterface $this */
        $provider->consumedBy($this, $wireName);
        if ($wireName) {
            $this->providers[$wireName] = $provider;
        } else {
            $this->providers[] = $provider;
        }
    }

    /**
     * @var ConsumerInterface[]
     * @see \StephanSchuler\DataStream\Node\Consumer\ConsumerTrait::providedBy()
     */
    protected $consumers = [];

    /**
     * @param ConsumerInterface $consumer
     * @param string $wireName
     * @see \StephanSchuler\DataStream\Node\Consumer\ConsumerTrait::providedBy()
     */
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
     * @see \StephanSchuler\DataStream\Node\Consumer\ConsumerTrait::providedBy()
     */
    public function getConsumers()
    {
        return $this->consumers;
    }

    /**
     * @param $data
     * @see \StephanSchuler\DataStream\Node\Consumer\ConsumerTrait::providedBy()
     */
    protected function feedConsumers($data)
    {
        Scheduler::globalInstance()->schedule(function () use ($data) {

            foreach (($this->getConsumers()) as $wireName => $consumer) {
                yield;
                $consumer->consume($data, $wireName);
            }

        });
    }
}