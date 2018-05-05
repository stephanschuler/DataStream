<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Source;

use StephanSchuler\DataStream\Node\NodeInterface;
use StephanSchuler\DataStream\Node\Provider\ProviderInterface;

interface SourceInterface extends NodeInterface, ProviderInterface
{
    public function provide();
}