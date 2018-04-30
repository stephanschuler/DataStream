<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Provider;

use StephanSchuler\DataStream\Consumer\ConsumerInterface;
use StephanSchuler\DataStream\Node\NodeInterface;

interface ProviderInterface extends NodeInterface
{
    public function consumedBy(ConsumerInterface $consumer);

    /**
     * @return ConsumerInterface[]
     */
    public function getConsumers();

    public function provide();
}