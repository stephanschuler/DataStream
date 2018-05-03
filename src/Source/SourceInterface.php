<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Source;

use StephanSchuler\DataStream\Node\NodeInterface;
use StephanSchuler\DataStream\Provider\ProviderInterface;

interface SourceInterface extends NodeInterface, ProviderInterface
{
    public function provide();
}