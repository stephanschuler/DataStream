<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Provider;

use StephanSchuler\DataStream\Node\Consumer\ConsumerInterface;

interface ProviderInterface
{
    public function consumedBy(ConsumerInterface $consumer);

    /**
     * @return ConsumerInterface[]
     */
    public function getConsumers();
}