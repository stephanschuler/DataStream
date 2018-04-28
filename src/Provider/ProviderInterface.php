<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Provider;

use StephanSchuler\DataStream\Consumer\ConsumerInterface;

interface ProviderInterface
{
    public function consumedBy(ConsumerInterface $consumer);

    public function provide();
}