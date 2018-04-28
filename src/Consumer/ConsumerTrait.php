<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Consumer;

use StephanSchuler\DataStream\Provider\ProviderInterface;

trait ConsumerTrait
{
    public function providedBy(ProviderInterface $provider)
    {
        /** @var ConsumerInterface $this */
        $provider->consumedBy($this);
    }
}