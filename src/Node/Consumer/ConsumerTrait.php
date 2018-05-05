<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Consumer;

use StephanSchuler\DataStream\Node\Provider\ProviderInterface;

trait ConsumerTrait
{
    public function providedBy(ProviderInterface $provider)
    {
        /** @var ConsumerInterface $this */
        $provider->consumedBy($this);
    }
}