<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Consumer;

use StephanSchuler\DataStream\Node\Provider\ProviderInterface;

trait ConsumerTrait
{
    public function providedBy(ProviderInterface $provider, string $wireName = '')
    {
        /** @var ConsumerInterface $this */
        $provider->consumedBy($this, $wireName);
    }

    public function dependsOn(): array
    {
        return [];
    }
}