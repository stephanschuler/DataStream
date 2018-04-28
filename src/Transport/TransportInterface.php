<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

use StephanSchuler\DataStream\Consumer\ConsumerInterface;
use StephanSchuler\DataStream\Provider\ProviderInterface;

interface TransportInterface extends ConsumerInterface, ProviderInterface
{
}