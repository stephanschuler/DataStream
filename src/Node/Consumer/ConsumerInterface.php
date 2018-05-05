<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Consumer;

use StephanSchuler\DataStream\Node\NodeInterface;
use StephanSchuler\DataStream\Node\Provider\ProviderInterface;

interface ConsumerInterface extends NodeInterface
{
    public function providedBy(ProviderInterface $provider);

    public function consume($data);
}