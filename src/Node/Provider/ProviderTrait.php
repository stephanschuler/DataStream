<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Provider;

use StephanSchuler\DataStream\Node\Consumer\ConsumerInterface;

trait ProviderTrait
{
    /**
     * @var ConsumerInterface[]
     */
    protected $consumers = [];

    public function consumedBy(ConsumerInterface $consumer)
    {
        $this->consumers[] = $consumer;
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
        foreach ($this->getConsumers() as $consumer) {
            $consumer->consume($data);
        }
    }
}