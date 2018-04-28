<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Consumer;

use StephanSchuler\DataStream\Provider\ProviderInterface;

interface ConsumerInterface
{
    public function providedBy(ProviderInterface $provider);

    public function consume($data);
}