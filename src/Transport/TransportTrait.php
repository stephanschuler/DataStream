<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

use StephanSchuler\DataStream\Consumer\ConsumerInterface;
use StephanSchuler\DataStream\Provider\ProviderInterface;

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
        foreach ($this->getConsumers() as $consumer) {
            $consumer->consume($data);
        }
    }
}