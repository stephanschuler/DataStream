<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Transport;

use StephanSchuler\DataStream\Node\Consumer\ConsumerInterface;
use StephanSchuler\DataStream\Node\NodeInterface;
use StephanSchuler\DataStream\Node\Provider\ProviderInterface;

interface TransportInterface extends NodeInterface, ConsumerInterface, ProviderInterface
{
}