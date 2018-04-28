<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Consumer;

interface StatefulInterface
{
    public function get();
}