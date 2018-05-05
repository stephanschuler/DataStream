<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Consumer;

interface StatefulInterface
{
    public function get();
}